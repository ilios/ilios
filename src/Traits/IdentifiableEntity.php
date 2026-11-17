<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * @property ?int $id
 */
trait IdentifiableEntity
{
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
