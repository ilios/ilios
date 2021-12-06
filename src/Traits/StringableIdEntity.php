<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Class StringableIdEntity
 */
trait StringableIdEntity
{
    public function __toString(): string
    {
        return (string) $this->id;
    }
}
