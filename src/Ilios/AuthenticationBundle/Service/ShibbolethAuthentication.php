<?php

namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\AuthenticationInterface as AuthenticationEntityInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;

class ShibbolethAuthentication implements AuthenticationInterface
{
    /**
     * @var AuthenticationManagerInterface
     */
    protected $authManager;
    
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;
    
    /**
     * @var JsonWebTokenManager
     */
    protected $jwtManager;
    
    
    public function __construct(
        AuthenticationManagerInterface $authManager,
        TokenStorageInterface $tokenStorage,
        JsonWebTokenManager $jwtManager
    ) {
        $this->authManager = $authManager;
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager = $jwtManager;
    }
    
    /**
     * Authenticate a user from shibboleth
     *
     * If the user is not yet logged in send a redirect Request
     * If the uesr is logged in, but no account exists send an error
     * If the user is autehtnciated send a JWT
     * @param Request $request
     *
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
            throw new \Exception(
                "No 'eepn' found for authenticated user.  Dump of SERVER global: " .
                var_export($_SERVER, true)
            );
        }
        $authEntity = $this->authManager->findAuthenticationBy(array('eppn' => $eppn));
        if (!$authEntity) {
            return new JsonResponse(array(
                'status' => 'noAccountExists',
                'eppn' => $eppn,
                'errors' => [],
                'jwt' => null,
            ), JsonResponse::HTTP_BAD_REQUEST);
        }
        if ($authEntity) {
            $token = $this->jwtManager->buildToken($authEntity->getUser());
            $this->tokenStorage->setToken($token);

            return new JsonResponse(array(
                'status' => 'success',
                'errors' => [],
                'jwt' => $token->getJwt(),
            ), JsonResponse::HTTP_OK);
        }
    }
}
