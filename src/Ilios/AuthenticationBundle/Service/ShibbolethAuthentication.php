<?php

namespace Ilios\AuthenticationBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\AuthenticationBundle\Traits\AuthenticationService;

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
     * Constructor
     * @param AuthenticationManager $authManager
     * @param JsonWebTokenManager $jwtManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        AuthenticationManager $authManager,
        JsonWebTokenManager $jwtManager,
        LoggerInterface $logger
    ) {
        $this->authManager = $authManager;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
    }
    
    /**
     * Authenticate a user from shibboleth
     *
     * If the user is not yet logged in send a redirect Request
     * If the user is logged in, but no account exists send an error
     * If the user is authenticated send a JWT
     * @param Request $request
     *
     * @throws \Exception when the shibboleth attributes do not contain an eppn
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
        $eppn = $request->server->get('eppn');
        if (!$eppn) {
            $msg =  "No 'eppn' found for authenticated user.";
            $this->logger->error($msg, ['server vars' => var_export($_SERVER, true)]);
            throw new \Exception($msg);
        }
        /* @var \Ilios\CoreBundle\Entity\AuthenticationInterface $authEntity */
        $authEntity = $this->authManager->findOneBy(array('username' => $eppn));
        if ($authEntity) {
            $user = $authEntity->getUser();
            if ($user->isEnabled()) {
                $jwt = $this->jwtManager->createJwtFromUser($user);

                return $this->createSuccessResponseFromJWT($jwt);
            }
        }

        return new JsonResponse(array(
            'status' => 'noAccountExists',
            'eppn' => $eppn,
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
        $logoutUrl = $url . '/Shibboleth.sso/Logout';
        return new JsonResponse(array(
            'status' => 'redirect',
            'logoutUrl' => $logoutUrl

        ), JsonResponse::HTTP_OK);
    }
}
