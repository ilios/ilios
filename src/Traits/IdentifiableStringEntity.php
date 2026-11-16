<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * @property string $id
 */
trait IdentifiableStringEntity
{
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
