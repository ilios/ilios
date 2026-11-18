<?php

declare(strict_types=1);

namespace App\Entity;

interface EnableableEntityInterface
{
    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): void;
}
