<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class ActivatableEntity
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
