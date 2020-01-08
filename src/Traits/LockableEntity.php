<?php

namespace App\Traits;

/**
 * Class LockableEntity
 */
trait LockableEntity
{
    /**
     * @param bool $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }
}
