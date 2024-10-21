<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\ServiceTokenUser;
use App\Classes\ServiceTokenUserInterface;
use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ServiceTokenUserProvider implements UserProviderInterface
{
    public function __construct(protected ServiceTokenRepository $tokenRepository)
    {
    }

    public function supportsClass(string $class): bool
    {
        return ServiceTokenUser::class === $class;
    }

    public function refreshUser(UserInterface $user): ServiceTokenUserInterface
    {
        if (!$user instanceof ServiceTokenUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $user::class)
            );
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function loadUserByIdentifier(string $identifier): ServiceTokenUserInterface
    {
        /** @var ?ServiceTokenInterface $token */
        $token = $this->tokenRepository->findOneBy(['id' => (int) $identifier]);

        if (!$token) {
            throw new UserNotFoundException(
                sprintf('Service token "%s" does not exist.', $identifier)
            );
        }
        return new ServiceTokenUser($token);
    }

    public function createServiceTokenUserFromTokenId(int $tokenId): ServiceTokenUserInterface
    {
        $token = $this->tokenRepository->findOneBy(['id' => $tokenId]);
        return new ServiceTokenUser($token);
    }
}
