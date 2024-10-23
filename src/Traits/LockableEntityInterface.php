<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface LockableEntityInterface
 */
interface LockableEntityInterface
{
    public function isLocked(): bool;

    public function setLocked(bool $locked): void;
}
