<?php

declare(strict_types=1);

namespace App\Traits;

trait StringableIdEntity
{
    protected int $id;

    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
