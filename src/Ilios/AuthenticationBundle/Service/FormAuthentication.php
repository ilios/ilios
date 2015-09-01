<?php

namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\AuthenticationInterface as AuthenticationEntityInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;

class FormAuthentication implements AuthenticationInterface
{
    /**
     * @var AuthenticationManagerInterface
     */
    protected $authManager;
    
    /**
     * @var Encoder
     */
    protected $encoder;
    
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
        UserPasswordEncoderInterface $encoder,
        TokenStorageInterface $tokenStorage,
        JsonWebTokenManager $jwtManager
    ) {
        $this->authManager = $authManager;
        $this->encoder = $encoder;
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager = $jwtManager;
    }
    
    /**
     * Login a user using a username and password
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
                $passwordValid = $this->encoder->isPasswordValid($user, $password);
                if ($passwordValid) {
                    $token = $this->jwtManager->buildToken($user);
                    $this->tokenStorage->setToken($token);
                    $this->updateLegacyPassword($authEntity, $password);
                    
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
     * Update users to the new password encoding when they login
     * @param  AuthenticationInterface $authEntity
     * @param  string         $password
     */
    protected function updateLegacyPassword(AuthenticationEntityInterface $authEntity, $password)
    {
        if ($authEntity->isLegacyAccount()) {
            $authEntity->setPasswordSha256(null);
            $encodedPassword = $this->encoder->encodePassword($authEntity->getUser(), $password);
            $authEntity->setPasswordBcrypt($encodedPassword);
            $this->authManager->updateAuthentication($authEntity);
        }
    }
}
