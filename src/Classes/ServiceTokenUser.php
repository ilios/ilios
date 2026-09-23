<?php

declare(strict_types=1);

namespace App\Classes;

use App\Entity\ServiceTokenInterface;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use Deprecated;

class ServiceTokenUser implements ServiceTokenUserInterface
{
    public function __construct(protected ServiceTokenInterface $serviceToken)
    {
    }

    #[\Override]
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof ServiceTokenUserInterface) {
            return false;
        }

        return $user->getUserIdentifier() === $this->getUserIdentifier();
    }

    #[\Override]
    public function getRoles(): array
    {
        return [];
    }

    #[Deprecated]
    /**
     * @codeCoverageIgnore
     */
    public function eraseCredentials(): void
    {
        // not implemented.
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        return (string) $this->serviceToken->getId();
    }

    #[\Override]
    public function getId(): int
    {
        return $this->serviceToken->getId();
    }

    #[\Override]
    public function isEnabled(): bool
    {
        return $this->serviceToken->isEnabled();
    }

    #[\Override]
    public function getCreatedAt(): DateTime
    {
        return $this->serviceToken->getCreatedAt();
    }

    #[\Override]
    public function getExpiresAt(): DateTime
    {
        return $this->serviceToken->getExpiresAt();
    }
}
