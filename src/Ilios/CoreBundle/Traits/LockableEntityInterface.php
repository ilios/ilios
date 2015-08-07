<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface LockableEntityInterface
 * @package Ilios\CoreBundle\Traits
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
