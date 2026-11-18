<?php

declare(strict_types=1);

namespace App\Classes;

use DateTime;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ServiceTokenUserInterface extends UserInterface, EquatableInterface
{
    public function getId(): int;

    public function isEnabled(): bool;

    public function getCreatedAt(): DateTime;

    public function getExpiresAt(): DateTime;
}
