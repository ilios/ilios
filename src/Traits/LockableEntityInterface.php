<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface LockableEntityInterface
 */
interface LockableEntityInterface
{
    /**
     * @return bool
     */
    public function isLocked();

    /**
     * @param bool $locked
     */
    public function setLocked($locked);
}
