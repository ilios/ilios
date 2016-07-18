<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class ActivatableEntity
 * @package Ilios\CoreBundle\Traits
 */
trait ActivatableEntity
{
    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
