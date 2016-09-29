<?php

namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\AuthenticationBundle\Traits\AuthenticationService;

class LdapAuthentication implements AuthenticationInterface
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
     * @param AuthenticationManager $authManager
     * @param JsonWebTokenManager            $jwtManager
     * @param string                         $ldapHost         injected from configuration
     * @param string                         $ldapPort         injected from configuration
     * @param string                         $ldapBindTemplate injected from configuration
     */
    public function __construct(
        AuthenticationManager $authManager,
        JsonWebTokenManager $jwtManager,
        $ldapHost,
        $ldapPort,
        $ldapBindTemplate
    ) {
        $this->authManager = $authManager;
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
        $code = JsonResponse::HTTP_OK;
        $errors = [];
        if (!$username) {
            $errors[] = 'missingUsername';
            $code = JsonResponse::HTTP_BAD_REQUEST;
        }
        if (!$password) {
            $errors[] = 'missingPassword';
            $code = JsonResponse::HTTP_BAD_REQUEST;
        }
        
        if ($username && $password) {
            $authEntity = $this->authManager->findAuthenticationByUsername($username);
            if ($authEntity) {
                $user = $authEntity->getUser();
                if ($user->isEnabled()) {
                    $passwordValid = $this->checkLdapPassword($username, $password);
                    if ($passwordValid) {
                        $jwt = $this->jwtManager->createJwtFromUser($user);

                        return $this->createSuccessResponseFromJWT($jwt);
                    }
                }
            }
            $errors[] = 'badCredentials';
            $code = JsonResponse::HTTP_UNAUTHORIZED;
        }

        return new JsonResponse(array(
            'status' => 'error',
            'errors' => $errors,
            'jwt' => null,
        ), $code);
    }

    /**
     * Logout a user
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        return new JsonResponse(array(
            'status' => 'success'
        ), JsonResponse::HTTP_OK);
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
