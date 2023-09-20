<?php

declare(strict_types=1);

namespace App\Traits;

interface EnableableEntityInterface
{
    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): void;
}
