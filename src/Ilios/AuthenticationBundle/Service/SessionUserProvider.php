<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\SessionUser;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\UserInterface as IliosUser;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class SessionUserProvider implements UserProviderInterface
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * SessionUserProvider constructor.
     * @param UserManager $userManager
     */
    public function __construct(
        UserManager $userManager
    ) {
        $this->userManager = $userManager;
    }

    public function loadUserByUsername($userId)
    {
        /** @var IliosUser $user */
        $user = $this->userManager->findOneBy(['id' => $userId]);

        if ($user) {
            return new SessionUser($user);
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $userId)
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SessionUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return SessionUser::class === $class;
    }
}
