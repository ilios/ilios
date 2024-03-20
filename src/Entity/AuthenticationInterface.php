<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

interface AuthenticationInterface extends LoggableEntityInterface
{
    public function setUsername(string $username): void;
    public function getUsername(): ?string;

    public function setPasswordHash(string $passwordHash): void;
    public function getPasswordHash(): ?string;

    public function getPassword(): ?string;

    public function setUser(UserInterface $user): void;
    public function getUser(): UserInterface;


    public function setInvalidateTokenIssuedBefore(?DateTime $invalidateTokenIssuedBefore = null): void;
    public function getInvalidateTokenIssuedBefore(): ?DateTime;
}
