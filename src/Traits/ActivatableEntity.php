<?php

declare(strict_types=1);

namespace App\Traits;

trait ActivatableEntity
{
    protected bool $active;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active)
    {
        $this->active = $active;
    }
}
