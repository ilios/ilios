<?php

namespace Ilios\AuthenticationBundle\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;

class Authenticator implements SimpleFormAuthenticatorInterface
{
    private $encoder;
    private $jwtKey;

    public function __construct(UserPasswordEncoderInterface $encoder, $secret)
    {
        $this->encoder = $encoder;
        $this->jwtKey = $secret;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        try {
            $authentication = $userProvider->loadUserByUsername($token->getUsername());
            $user = $authentication->getUser();
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException('Invalid username or password');
        }

        $passwordValid = $this->encoder->isPasswordValid($user, $token->getCredentials());
        if ($passwordValid) {
            $token = new JwtToken($this->jwtKey);
            $token->setUser($user);

            return $token;
        }

        throw new AuthenticationException('Invalid username or password');
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken
            && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }
}
