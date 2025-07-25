<?php

declare(strict_types=1);

namespace App\Classes;

use App\Entity\ServiceTokenInterface;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

class ServiceTokenUser implements ServiceTokenUserInterface
{
    public function __construct(protected ServiceTokenInterface $serviceToken)
    {
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof ServiceTokenUserInterface) {
            return false;
        }

        return $user->getUserIdentifier() === $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        return [];
    }

    #[\Deprecated]
    /**
     * @codeCoverageIgnore
     */
    public function eraseCredentials(): void
    {
        // not implemented.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->serviceToken->getId();
    }

    public function getId(): int
    {
        return $this->serviceToken->getId();
    }

    public function isEnabled(): bool
    {
        return $this->serviceToken->isEnabled();
    }

    public function getCreatedAt(): DateTime
    {
        return $this->serviceToken->getCreatedAt();
    }

    public function getExpiresAt(): DateTime
    {
        return $this->serviceToken->getExpiresAt();
    }
}
