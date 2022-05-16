<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

interface AuthenticationInterface extends LoggableEntityInterface
{
    public function setUsername(string $username);
    public function getUsername(): ?string;

    public function setPasswordHash(string $passwordHash);
    public function getPasswordHash(): ?string;

    public function getPassword(): ?string;

    public function setUser(UserInterface $user);
    public function getUser(): UserInterface;


    public function setInvalidateTokenIssuedBefore(DateTime $invalidateTokenIssuedBefore = null);
    public function getInvalidateTokenIssuedBefore(): ?DateTime;
}
