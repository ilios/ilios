<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\SessionUser;
use App\Classes\SessionUserInterface;
use App\Entity\UserInterface as IliosUser;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class SessionUserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * SessionUserProvider constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param IliosUser $user
     * @return SessionUserInterface
     */
    public function createSessionUserFromUser(IliosUser $user): SessionUserInterface
    {
        return new SessionUser($user, $this->userRepository);
    }

    /**
     * @param int $userId
     * @return SessionUserInterface
     */
    public function createSessionUserFromUserId(int $userId): SessionUserInterface
    {
        /** @var IliosUser $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        return new SessionUser($user, $this->userRepository);
    }

    public function loadUserByUsername($userId)
    {
        /** @var IliosUser $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        if ($user) {
            return new SessionUser($user, $this->userRepository);
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
