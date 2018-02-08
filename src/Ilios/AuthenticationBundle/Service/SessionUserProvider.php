<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\SessionUser;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\UserInterface as IliosUser;

use Ilios\CoreBundle\Service\Config;
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
     * @var Config
     */
    protected $config;

    /**
     * SessionUserProvider constructor.
     * @param UserManager $userManager
     * @param Config $config
     */
    public function __construct(
        UserManager $userManager,
        Config $config
    ) {
        $this->userManager = $userManager;
        $this->config = $config;
    }


    public function createSessionUserFromUser(IliosUser $user) : SessionUserInterface
    {
        return new SessionUser($user, $this->userManager, $this->config);
    }

    public function loadUserByUsername($userId)
    {
        /** @var IliosUser $user */
        $user = $this->userManager->findOneBy(['id' => $userId]);

        if ($user) {
            return new SessionUser($user, $this->userManager, $this->config);
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
