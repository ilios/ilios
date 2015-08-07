<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class LockableEntity
 * @package Ilios\CoreBundle\Traits
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
