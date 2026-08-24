<?php

declare(strict_types=1);

namespace App\Entity;

use Stringable;

interface UserPreferenceInterface extends
    Stringable
{
    public function setUser(UserInterface $user): void;

    public function getUser(): UserInterface;

    public function setJson(string $json): void;

    public function getJson(): string;
}
