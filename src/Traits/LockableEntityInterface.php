<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface LockableEntityInterface
 */
interface LockableEntityInterface
{
    public function isLocked(): bool;

    /**
     * @param bool $locked
     */
    public function setLocked($locked);
}
