<?php
namespace Ilios\LegacyCIBundle\Authentication;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Ilios\LegacyCIBundle\Authentication\Token;

class Provider implements AuthenticationProviderInterface
{
    /**
     *
     * @var Symfony\Component\Security\Core\User\UserProviderInterface 
     */
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());
        if ($user) {
            $token->setUser($user);
            $token->setAuthenticated(true);
            return $token;
        }

        throw new AuthenticationException('The CI authentication failed.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof Token;
    }
}
