<?php

declare(strict_types=1);

namespace App\Traits;

trait EnableableEntity
{
    protected bool $enabled;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
