<?php

declare(strict_types=1);

namespace App\Traits;

interface StringableEntityToIdInterface
{
    public function __toString(): string;
}
