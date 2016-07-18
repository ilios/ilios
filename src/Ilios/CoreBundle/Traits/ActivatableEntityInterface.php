<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface ActivatableEntityInterface
 * @package Ilios\CoreBundle\Traits
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
