<?php

namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;

class LdapAuthentication implements AuthenticationInterface
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
    
    /**
     * @var string
     */
    protected $ldapHost;
    
    /**
     * @var string
     */
    protected $ldapPort;
    
    /**
     * @var string
     */
    protected $ldapBindTemplate;
    
    /**
     * Constructor
     * @param AuthenticationManagerInterface $authManager
     * @param TokenStorageInterface          $tokenStorage
     * @param JsonWebTokenManager            $jwtManager
     * @param string                         $ldapHost         injected from configuration
     * @param string                         $ldapPort         injected from configuration
     * @param string                         $ldapBindTemplate injected from configuration
     */
    public function __construct(
        AuthenticationManagerInterface $authManager,
        TokenStorageInterface $tokenStorage,
        JsonWebTokenManager $jwtManager,
        $ldapHost,
        $ldapPort,
        $ldapBindTemplate
    ) {
        $this->authManager = $authManager;
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager = $jwtManager;
        $this->ldapHost = $ldapHost;
        $this->ldapPort = $ldapPort;
        $this->ldapBindTemplate = $ldapBindTemplate;
    }
    
    /**
     * Login a user using a username and password
     * to bind against an LDAP server
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $errors = [];
        if (!$username) {
            $errors[] = 'missingUsername';
        }
        if (!$password) {
            $errors[] = 'missingPassword';
        }
        
        if ($username && $password) {
            $authEntity = $this->authManager->findAuthenticationByUsername($username);
            if ($authEntity) {
                $user = $authEntity->getUser();
                $passwordValid = $this->checkLdapPassword($username, $password);
                if ($passwordValid) {
                    $token = $this->jwtManager->buildToken($user);
                    $this->tokenStorage->setToken($token);
                    
                    return new JsonResponse(array(
                        'status' => 'success',
                        'errors' => [],
                        'jwt' => $token->getJwt(),
                    ), JsonResponse::HTTP_OK);
                }
            }
            $errors[] = 'badCredentials';
        }

        return new JsonResponse(array(
            'status' => 'error',
            'errors' => $errors,
            'jwt' => null,
        ), JsonResponse::HTTP_BAD_REQUEST);
    }
    
    /**
     * Check against ldap to see if the user is valid
     * @param  string $username
     * @param  string $password
     *
     * @return boolean
     */
    public function checkLdapPassword($username, $password)
    {
        $ldapConn = @ldap_connect($this->ldapHost, $this->ldapPort);
        if ($ldapConn) {
            $ldapRdn = sprintf($this->ldapBindTemplate, $username);
            $ldapBind = @ldap_bind($ldapConn, $ldapRdn, $password);
            if ($ldapBind) {
                return true;
            }
        }
        
        return false;
    }
}
