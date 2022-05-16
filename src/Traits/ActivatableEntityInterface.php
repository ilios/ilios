<?php

declare(strict_types=1);

namespace App\Traits;

interface ActivatableEntityInterface
{
    public function isActive(): bool;

    public function setActive(bool $active);
}
