<?php

namespace Ilios\AuthenticationBundle\Service;

use AppBundle\Classes\SessionUser;
use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\Manager\UserManager;
use AppBundle\Entity\UserInterface as IliosUser;

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

    /**
     * @param IliosUser $user
     * @return SessionUserInterface
     */
    public function createSessionUserFromUser(IliosUser $user) : SessionUserInterface
    {
        return new SessionUser($user, $this->userManager);
    }

    /**
     * @param int $userId
     * @return SessionUserInterface
     */
    public function createSessionUserFromUserId(int $userId) : SessionUserInterface
    {
        /** @var IliosUser $user */
        $user = $this->userManager->findOneBy(['id' => $userId]);
        return new SessionUser($user, $this->userManager);
    }

    public function loadUserByUsername($userId)
    {
        /** @var IliosUser $user */
        $user = $this->userManager->findOneBy(['id' => $userId]);

        if ($user) {
            return new SessionUser($user, $this->userManager);
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
