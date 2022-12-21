<?php

declare(strict_types=1);

namespace App\Traits;

interface StringableEntityInterface
{
    public function __toString(): string;
}
