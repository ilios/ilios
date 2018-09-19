<?php

namespace App\Traits;

/**
 * Interface ActivatableEntityInterface
 */
interface ActivatableEntityInterface
{
    /**
     * @return boolean
     */
    public function isActive();

    /**
     * @param boolean $active
     */
    public function setActive($active);
}
