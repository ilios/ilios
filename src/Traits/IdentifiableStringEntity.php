<?php

declare(strict_types=1);

namespace App\Traits;

trait IdentifiableStringEntity
{
    protected string $id;

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
