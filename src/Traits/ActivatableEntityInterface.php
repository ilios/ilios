<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface ActivatableEntityInterface
 */
interface ActivatableEntityInterface
{
    public function isActive(): bool;

    public function setActive($active);
}
