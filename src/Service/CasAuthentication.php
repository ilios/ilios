<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\AuthenticationRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Traits\AuthenticationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Exception;
use UnexpectedValueException;

/**
 * Authenticate user using CAS Protocol and return a JWT
 */
class CasAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

    protected const string REDIRECT_COOKIE = 'ilios-cas-redirect';
    protected const string JWT_COOKIE = 'ilios-cas-jwt';
    protected const string NO_ACCOUNT_EXISTS_COOKIE = 'ilios-cas-no-account-exists';

    /**
     * Constructor
     */
    public function __construct(
        protected AuthenticationRepository $authenticationRepository,
        protected JsonWebTokenManager $jwtManager,
        protected LoggerInterface $logger,
        protected RouterInterface $router,
        protected CasManager $casManager,
        protected SessionUserProvider $sessionUserProvider,
        protected string $kernelSecret,
    ) {
    }

    /**
     * Authenticate a user from CAS
     *
     * If a JWT cookie exists use it to create a JSON response
     * If the user account doesn't exist send a JSON response with that error
     * If the user is not yet logged in send a redirect Request
     * If the user is logged in, but no account exists set a cookie and redirect them back to the frontend
     * If the user is authenticated set a cookie and redirect back to the frontend
     */
    public function login(Request $request): Response
    {
        if ($request->cookies->has(self::JWT_COOKIE)) {
            $jwt = $request->cookies->get(self::JWT_COOKIE);
            try {
                $this->jwtManager->getUserIdFromToken($jwt);
                return $this->createSuccessResponseFromJWT($jwt);
            } catch (UnexpectedValueException) {
                //JWT could not be validated, move on
            }
        }
        if ($request->cookies->has(self::NO_ACCOUNT_EXISTS_COOKIE)) {
            $response = $this->createNoAccountExistsResponse($request->cookies->get(self::NO_ACCOUNT_EXISTS_COOKIE));
            $response->headers->clearCookie(self::NO_ACCOUNT_EXISTS_COOKIE);

            return $response;
        }
        $ticket = $request->query->has('ticket') ? $request->query->all()['ticket'] : null;

        if (!$ticket) {
            return new JsonResponse([
                'status' => 'redirect',
                'errors' => [],
                'jwt' => null,
            ], JsonResponse::HTTP_OK);
        }

        $username = $this->casManager->getUsername($this->getServiceUrl(), $ticket);
        if (!$username) {
            $msg =  "No user found for authenticated user.";
            $this->logger->error($msg, ['server vars' => var_export($_SERVER, true)]);
            throw new Exception($msg);
        }
        /* @var \App\Entity\AuthenticationInterface $authEntity */
        $authEntity = $this->authenticationRepository->findOneBy(['username' => $username]);
        if ($authEntity) {
            $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($authEntity->getUser());
            if ($sessionUser->isEnabled()) {
                $jwt = $this->jwtManager->createJwtFromSessionUser($sessionUser);
                $response = $this->createSuccessResponseFromJWT($jwt);

                if ($request->cookies->has(self::REDIRECT_COOKIE)) {
                    $value = $request->cookies->get(self::REDIRECT_COOKIE);
                    [$providedHash, $redirectUrl] = json_decode($value, associative: true, depth: 2);
                    if (is_string($providedHash) && filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                        $signature = $this->generateSignature($redirectUrl);
                        //validate the signature to ensure the redirect hasn't been tampered with
                        if (hash_equals($signature, $providedHash)) {
                            $response = new RedirectResponse($redirectUrl);
                        } else {
                            $this->logger->error(
                                "Invalid signature in redirect cookie. " .
                                "This is shady and may indicate someone is attempting " .
                                "to use our redirect cookie for something nefarious. "
                            );
                        }
                    }
                    $response->headers->clearCookie(self::REDIRECT_COOKIE);
                }

                $exp = $this->jwtManager->getExpiresAtFromToken($jwt);
                $response->headers->setCookie(Cookie::create(
                    self::JWT_COOKIE,
                    $jwt,
                    $exp
                ));

                return $response;
            }
        }

        $response = new RedirectResponse($this->getRootUrl());
        $response->headers->setCookie(Cookie::create(
            self::NO_ACCOUNT_EXISTS_COOKIE,
            $username,
            strtotime('now + 45 seconds')
        ));
        //just in case the redirect cookie hasn't expired, we should trash it
        $response->headers->removeCookie(self::REDIRECT_COOKIE);

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function logout(Request $request): JsonResponse
    {
        $logoutUrl = $this->casManager->getLogoutUrl();
        $response =  new JsonResponse([
            'status' => 'redirect',
            'logoutUrl' => $logoutUrl,
        ], JsonResponse::HTTP_OK);
        $response->headers->clearCookie(self::JWT_COOKIE);

        return $response;
    }

    public function getPublicConfigurationInformation(Request $request): array
    {
        $configuration = [];
        $configuration['type'] = 'cas';
        $configuration['casLoginUrl'] = $this->casManager->getLoginUrl();

        return $configuration;
    }

    public function createAuthenticationResponse(Request $request): Response
    {
        if (
            $request->cookies->has(self::NO_ACCOUNT_EXISTS_COOKIE) ||
            $request->cookies->has(self::JWT_COOKIE)
        ) {
            return new Response();
        }

        $service = $this->getServiceUrl();
        $url = $this->casManager->getLoginUrl() . "?service={$service}";
        $response = new RedirectResponse($url);

        if (!$request->cookies->has(self::REDIRECT_COOKIE)) {
            $redirectUrl = $this->getAllowedRedirectUrl($request);

            $signature = $this->generateSignature($redirectUrl);
            //store the redirect along with a signature to ensure it hasn't been tampered with
            $value = json_encode([$signature, $redirectUrl]);
            $response->headers->setCookie(Cookie::create(
                name: self::REDIRECT_COOKIE,
                value: $value,
                expire: strtotime('+2 minutes'),
            ));
        }

        return $response;
    }

    /**
     * Always use https://SERVER/auth/login for the CAS service
     * This ensures users get redirected back there securely when authentication is successful
     * and it also ensures that the ticket is valid since it is based on the service URL.
     */
    protected function getServiceUrl(): string
    {
        $url =  $this->router->generate(
            'app_auth_login',
            [],
            UrlGenerator::NETWORK_PATH
        );

        return "https:{$url}";
    }

    protected function getRootUrl(): string
    {
        return $this->router->generate(
            'app_index_index',
            [],
            UrlGenerator::ABSOLUTE_URL
        );
    }

    protected function createNoAccountExistsResponse(string $username): JsonResponse
    {
        return new JsonResponse([
            'status' => 'noAccountExists',
            'userId' => $username,
            'errors' => [],
            'jwt' => null,
        ], JsonResponse::HTTP_OK);
    }

    protected function getAllowedRedirectUrl(Request $request): string
    {
        $topLevelRoutes = [
            'admin',
            'courses',
            'curriculum-inventory-reports',
            'dashboard',
            'data',
            'events',
            'instructorgroups',
            'learnergroups',
            'login',
            'lm',
            'myprofile',
            'mymaterials',
            'program',
            'schools',
            'search',
        ];
        $or = implode('|', $topLevelRoutes);
        $pattern = "+^/({$or})/?[\/a-z\-0-9]*$+i";
        if (preg_match($pattern, $request->getRequestUri())) {
            return $this->getRootUrl() . ltrim($request->getRequestUri(), '/');
        } else {
            return $this->getRootUrl();
        }
    }

    /**
     * Build a HMAC signature for a value
     * We use a combination of the secret and a string to build a key and then hash the value,
     * the result is binary, so we have to pass it through the sodium_bin2hex function to get a string.
     * This results in a signature that is unique for the value and cannot be duplicated without the secret.
     */
    protected function generateSignature(string $value): string
    {
        return sodium_bin2hex(
            sodium_crypto_generichash($value, $this->kernelSecret . self::REDIRECT_COOKIE)
        );
    }
}
