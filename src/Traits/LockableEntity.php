<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Class LockableEntity
 */
trait LockableEntity
{
    protected bool $locked;

    /**
     * @param bool $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }
}
