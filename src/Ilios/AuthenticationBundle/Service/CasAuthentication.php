<?php

namespace Ilios\AuthenticationBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\AuthenticationBundle\Traits\AuthenticationService;

/**
 * Class CasAuthentication
 */
class CasAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

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
     * @var Router
     */
    protected $router;

    /**
     * @var CasManager
     */
    protected $casManager;

    /**
     * Constructor
     * @param AuthenticationManager $authManager
     * @param JsonWebTokenManager $jwtManager
     * @param LoggerInterface $logger
     * @param Router $router
     * @param CasManager $casManager
     */
    public function __construct(
        AuthenticationManager $authManager,
        JsonWebTokenManager $jwtManager,
        LoggerInterface $logger,
        Router $router,
        CasManager $casManager
    ) {
        $this->authManager = $authManager;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
        $this->router = $router;
        $this->casManager = $casManager;
    }
    
    /**
     * Authenticate a user from shibboleth
     *
     * If the user is not yet logged in send a redirect Request
     * If the user is logged in, but no account exists send an error
     * If the user is authenticated send a JWT
     * @param Request $request
     *
     * @throws \Exception when the shibboleth attributes do not contain a value for the configured user id attribute
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $service = $request->query->get('service');
        $ticket = $request->query->get('ticket');

        if (!$ticket) {
            return new JsonResponse([
                'status' => 'redirect',
                'errors' => [],
                'jwt' => null,
            ], JsonResponse::HTTP_OK);
        }

        $userId = $this->casManager->getUserId($service, $ticket);
        if (!$userId) {
            $msg =  "No user found for authenticated user.";
            $this->logger->error($msg, ['server vars' => var_export($_SERVER, true)]);
            throw new \Exception($msg);
        }
        /* @var \Ilios\CoreBundle\Entity\AuthenticationInterface $authEntity */
        $authEntity = $this->authManager->findOneBy(['username' => $userId]);
        if ($authEntity) {
            $sessionUser = $authEntity->getSessionUser();
            if ($sessionUser->isEnabled()) {
                $jwt = $this->jwtManager->createJwtFromSessionUser($sessionUser);

                return $this->createSuccessResponseFromJWT($jwt);
            }
        }

        return new JsonResponse([
            'status' => 'noAccountExists',
            'userId' => $userId,
            'errors' => [],
            'jwt' => null,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Logout a user
     * @param Request $request
     *
     * @return JsonResponse
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
}
