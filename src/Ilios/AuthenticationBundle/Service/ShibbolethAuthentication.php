<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\CoreBundle\Service\Config;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\AuthenticationBundle\Traits\AuthenticationService;

/**
 * Class ShibbolethAuthentication
 */
class ShibbolethAuthentication implements AuthenticationInterface
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
     * @var String
     */
    protected $logoutPath;

    /**
     * @var String
     */
    protected $loginPath;

    /**
     * @var String
     */
    protected $userIdAttribute;

    /**
     * Constructor
     * @param AuthenticationManager $authManager
     * @param JsonWebTokenManager $jwtManager
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        AuthenticationManager $authManager,
        JsonWebTokenManager $jwtManager,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->authManager = $authManager;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
        $this->logoutPath = $config->get('shibboleth_authentication_logout_path');
        $this->loginPath = $config->get('shibboleth_authentication_login_path');
        $this->userIdAttribute = $config->get('shibboleth_authentication_user_id_attribute');
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
        $applicationId = $request->server->get('Shib-Application-ID');
        if (!$applicationId) {
            return new JsonResponse([
                'status' => 'redirect',
                'errors' => [],
                'jwt' => null,
            ], JsonResponse::HTTP_OK);
        }
        $userId = $request->server->get($this->userIdAttribute);
        if (!$userId) {
            $msg =  "No '{$this->userIdAttribute}' found for authenticated user.";
            $logVars = [];

            $shibProperties = [
                'Shib-Session-ID',
                'Shib-Authentication-Instant',
                'Shib-Authentication-Method',
                'Shib-Session-Index'
            ];
            foreach ($shibProperties as $key) {
                $logVars[$key] = $request->server->get($key);
            }

            $logVars['HTTP_REFERER'] = $request->server->get('HTTP_REFERER');
            $logVars['REMOTE_ADDR'] = $request->server->get('REMOTE_ADDR');

            $this->logger->info($msg, ['server variables' => var_export($logVars, true)]);

            return new JsonResponse([
                'status' => 'redirect',
                'errors' => [],
                'jwt' => null,
            ], JsonResponse::HTTP_OK);
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
        $url = $request->getSchemeAndHttpHost();
        $logoutUrl = $url . $this->logoutPath;
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }
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
        $configuration['type'] = 'shibboleth';
        $url = $request->getSchemeAndHttpHost();
        $configuration['loginUrl'] = $url . $this->loginPath;

        return $configuration;
    }
}
