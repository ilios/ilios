<?php

declare(strict_types=1);

namespace App\Entity;

interface IdentifiableEntityInterface
{
    public function setId(int $id): void;
    public function getId(): ?int;
}
