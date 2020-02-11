<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Manager\AuthenticationManager;
use App\Traits\AuthenticationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Exception;

/**
 * Authenticate user using CAS Protocol and return a JWT
 */
class CasAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

    protected const REDIRECT_COOKIE = 'ilios_cas_redirect';
    protected const JWT_COOKIE = 'ilios_jwt';

    /**
     * @var AuthenticationManager
     */
    protected $authManager;

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
     * @param AuthenticationManager $authManager
     * @param JsonWebTokenManager $jwtManager
     * @param LoggerInterface $logger
     * @param RouterInterface $router
     * @param CasManager $casManager
     * @param SessionUserProvider $sessionUserProvider
     */
    public function __construct(
        AuthenticationManager $authManager,
        JsonWebTokenManager $jwtManager,
        LoggerInterface $logger,
        RouterInterface $router,
        CasManager $casManager,
        SessionUserProvider $sessionUserProvider
    ) {
        $this->authManager = $authManager;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
        $this->router = $router;
        $this->casManager = $casManager;
        $this->sessionUserProvider = $sessionUserProvider;
    }

    /**
     * Authenticate a user from CAS
     *
     * If the user has been authenticated already send the stored JWT
     * If the user is not yet logged in send a redirect Request
     * If the user is logged in, but no account exists send an error
     * If the user is authenticated send a JWT
     */
    public function login(Request $request)
    {
        if ($request->cookies->has(self::JWT_COOKIE)) {
            $response = $this->createSuccessResponseFromJWT($request->cookies->get(self::JWT_COOKIE));
            $response->headers->clearCookie(self::JWT_COOKIE);

            return $response;
        }
        $ticket = $request->query->get('ticket');

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
        $authEntity = $this->authManager->findOneBy(['username' => $username]);
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
                $response->headers->setCookie(Cookie::create(
                    self::JWT_COOKIE,
                    $jwt,
                    strtotime('now + 45 seconds')
                ));

                return $response;
            }
        }

        return new JsonResponse([
            'status' => 'noAccountExists',
            'userId' => $username,
            'errors' => [],
            'jwt' => null,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @inheritDoc
     */
    public function logout(Request $request)
    {
        $logoutUrl = $this->casManager->getLogoutUrl();
        return new JsonResponse([
            'status' => 'redirect',
            'logoutUrl' => $logoutUrl
        ], JsonResponse::HTTP_OK);
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
        $cookie = $request->cookies->get(self::JWT_COOKIE);
        if (!$cookie) {
            $originalPath = $request->getSchemeAndHttpHost() . $request->getRequestUri();
            $service = $this->getServiceUrl();
            $url = $this->casManager->getLoginUrl() . "?service=${service}";
            $response = RedirectResponse::create($url);
            $response->headers->setCookie(Cookie::create(self::REDIRECT_COOKIE, $originalPath));

            return $response;
        }

        return new Response();
    }

    /**
     * Always user /auth/login for the CAS service
     * This ensures users get redirected back there when authentication is successfull
     * it also ensures that the ticket is valid since it is based on the service.
     */
    protected function getServiceUrl(): string
    {
        return $this->router->generate(
            'ilios_authentication.login',
            [],
            UrlGenerator::ABSOLUTE_URL
        );
    }
}
