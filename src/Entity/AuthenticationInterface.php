<?php

declare(strict_types=1);

namespace App\Entity;

use App\Classes\SessionUserInterface;
use DateTime;

/**
 * Interface AuthenticationInterface
 */
interface AuthenticationInterface extends LoggableEntityInterface
{
    /**
     * @param string $username
     */
    public function setUsername($username);

    public function getUsername(): string;

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash($passwordHash);

    public function getPasswordHash(): ?string;

    public function getPassword(): string;

    public function setUser(UserInterface $user);

    public function getUser(): UserInterface;


    public function setInvalidateTokenIssuedBefore(DateTime $invalidateTokenIssuedBefore = null);

    public function getInvalidateTokenIssuedBefore(): ?DateTime;
}
