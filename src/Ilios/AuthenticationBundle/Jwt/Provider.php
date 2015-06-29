<?php
namespace Ilios\AuthenticationBundle\Jwt;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Util\StringUtils;

use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;

class Provider implements AuthenticationProviderInterface
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getCredentials());

        if ($user) {
            $token->setUser($user);

            return $token;
        }

        throw new AuthenticationException('Unable to get user for this token');
    }


    public function supports(TokenInterface $token)
    {
        return $token instanceof JwtToken;
    }
}
