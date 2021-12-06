<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Class ActivatableEntity
 */
trait ActivatableEntity
{
    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }
}
