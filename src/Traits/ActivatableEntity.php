<?php

namespace App\Traits;

/**
 * Class ActivatableEntity
 */
trait ActivatableEntity
{
    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
