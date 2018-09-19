<?php

namespace App\Traits;

/**
 * Class LockableEntity
 */
trait LockableEntity
{
    /**
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return boolean
     */
    public function isLocked()
    {
        return $this->locked;
    }
}
