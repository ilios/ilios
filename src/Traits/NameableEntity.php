<?php

declare(strict_types=1);

namespace App\Traits;

trait NameableEntity
{
    protected string $name;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
