<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface ActivatableEntityInterface
 */
interface ActivatableEntityInterface
{
    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param bool $active
     */
    public function setActive($active);
}
