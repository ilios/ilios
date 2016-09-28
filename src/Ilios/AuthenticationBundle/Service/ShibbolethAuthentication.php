<?php

namespace Ilios\AuthenticationBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\AuthenticationBundle\Traits\AuthenticationService;

/**
 * Class ShibbolethAuthentication
 * @package Ilios\AuthenticationBundle\Service
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
    protected $userIdAttribute;

    /**
     * Constructor
     * @param AuthenticationManager $authManager
     * @param JsonWebTokenManager $jwtManager
     * @param LoggerInterface $logger
     * @param String $logoutPath
     * @param String $userIdAttribute
     */
    public function __construct(
        AuthenticationManager $authManager,
        JsonWebTokenManager $jwtManager,
        LoggerInterface $logger,
        $logoutPath,
        $userIdAttribute
    ) {
        $this->authManager = $authManager;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
        $this->logoutPath = $logoutPath;
        $this->userIdAttribute = $userIdAttribute;
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
            return new JsonResponse(array(
                'status' => 'redirect',
                'errors' => [],
                'jwt' => null,
            ), JsonResponse::HTTP_OK);
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

            $this->logger->error($msg, ['server variables' => var_export($logVars, true)]);
            throw new \Exception($msg);
        }
        /* @var \Ilios\CoreBundle\Entity\AuthenticationInterface $authEntity */
        $authEntity = $this->authManager->findOneBy(array('username' => $userId));
        if ($authEntity) {
            $user = $authEntity->getUser();
            if ($user->isEnabled()) {
                $jwt = $this->jwtManager->createJwtFromUser($user);

                return $this->createSuccessResponseFromJWT($jwt);
            }
        }

        return new JsonResponse(array(
            'status' => 'noAccountExists',
            'userId' => $userId,
            'errors' => [],
            'jwt' => null,
        ), JsonResponse::HTTP_OK);
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
        return new JsonResponse(array(
            'status' => 'redirect',
            'logoutUrl' => $logoutUrl

        ), JsonResponse::HTTP_OK);
    }
}
