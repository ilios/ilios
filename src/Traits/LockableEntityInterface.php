<?php

namespace App\Traits;

/**
 * Interface LockableEntityInterface
 */
interface LockableEntityInterface
{
    /**
     * @return boolean
     */
    public function isLocked();

    /**
     * @param boolean $locked
     */
    public function setLocked($locked);
}
