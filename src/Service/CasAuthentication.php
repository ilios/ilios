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

    protected const REDIRECT_COOKIE = 'ilios-cas-redirect';
    protected const JWT_COOKIE = 'ilios-cas-jwt';
    protected const NO_ACCOUNT_EXISTS_COOKIE = 'ilios-cas-no-account-exists';

    protected AuthenticationRepository $authenticationRepository;

    /**
     * @var JsonWebTokenManager
     */
    protected $jwtManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var CasManager
     */
    protected $casManager;

    /**
     * @var SessionUserProvider
     */
    protected $sessionUserProvider;

    /**
     * Constructor
     */
    public function __construct(
        AuthenticationRepository $authenticationRepository,
        JsonWebTokenManager $jwtManager,
        LoggerInterface $logger,
        RouterInterface $router,
        CasManager $casManager,
        SessionUserProvider $sessionUserProvider
    ) {
        $this->authenticationRepository = $authenticationRepository;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
        $this->router = $router;
        $this->casManager = $casManager;
        $this->sessionUserProvider = $sessionUserProvider;
    }

    /**
     * Authenticate a user from CAS
     *
     * If a JWT cookie exists user it to create a JSON response
     * If the user account doesn't exist send a JSON response with that error
     * If the user is not yet logged in send a redirect Request
     * If the user is logged in, but no account exists set a cookie and redirect them back to the frontend
     * If the user is authenticated set a cookie and redirect back to the frontend
     */
    public function login(Request $request)
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
                if ($request->cookies->has(self::REDIRECT_COOKIE)) {
                    $response = RedirectResponse::create($request->cookies->get(self::REDIRECT_COOKIE));
                    $response->headers->clearCookie(self::REDIRECT_COOKIE);
                } else {
                    $response = $this->createSuccessResponseFromJWT($jwt);
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

        if ($request->cookies->has(self::REDIRECT_COOKIE)) {
            $url = $request->cookies->get(self::REDIRECT_COOKIE);
        } else {
            $url = $this->getRootUrl();
        }

        $response = RedirectResponse::create($url);
        $response->headers->setCookie(Cookie::create(
            self::NO_ACCOUNT_EXISTS_COOKIE,
            $username,
            strtotime('now + 45 seconds')
        ));

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function logout(Request $request)
    {
        $logoutUrl = $this->casManager->getLogoutUrl();
        $response =  new JsonResponse([
            'status' => 'redirect',
            'logoutUrl' => $logoutUrl
        ], JsonResponse::HTTP_OK);
        $response->headers->clearCookie(self::JWT_COOKIE);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getPublicConfigurationInformation(Request $request)
    {
        $configuration = [];
        $configuration['type'] = 'cas';
        $configuration['casLoginUrl'] = $this->casManager->getLoginUrl();

        return $configuration;
    }

    /**
     * @inheritdoc
     */
    public function createAuthenticationResponse(Request $request): Response
    {
        if (
            $request->cookies->has(self::NO_ACCOUNT_EXISTS_COOKIE) ||
            $request->cookies->has(self::JWT_COOKIE)
        ) {
            return new Response();
        }

        $service = $this->getServiceUrl();
        $url = $this->casManager->getLoginUrl() . "?service=${service}";
        $response = RedirectResponse::create($url);

        if (!$request->cookies->has(self::REDIRECT_COOKIE)) {
            $redirectUrl = $this->getAllowedRedirectUrl($request);
            $response->headers->setCookie(Cookie::create(self::REDIRECT_COOKIE, $redirectUrl));
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
            'ilios_authentication.login',
            [],
            UrlGenerator::NETWORK_PATH
        );

        return "https:${url}";
    }

    protected function getRootUrl(): string
    {
        return $this->router->generate(
            'ilios_index',
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
        $pattern = "+^/(${or})/?[\/a-z\-0-9]*$+i";
        if (preg_match($pattern, $request->getRequestUri())) {
            return $this->getRootUrl() . ltrim($request->getRequestUri(), '/');
        } else {
            return $this->getRootUrl();
        }
    }
}
