<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * @property int $id
 */
trait StringableIdEntity
{
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
