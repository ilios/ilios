<?php

declare(strict_types=1);

namespace App\Entity;

interface ActivatableEntityInterface
{
    public function isActive(): bool;

    public function setActive(bool $active): void;
}
