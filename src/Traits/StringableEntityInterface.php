<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * interface StringableEntityInterface
 */
interface StringableEntityInterface
{
    /**
    * @return string
    */
    public function __toString(): string;
}
