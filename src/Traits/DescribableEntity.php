<?php

declare(strict_types=1);

namespace App\Traits;

trait DescribableEntity
{
    protected string $description;

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
