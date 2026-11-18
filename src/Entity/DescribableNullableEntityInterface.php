<?php

declare(strict_types=1);

namespace App\Entity;

interface DescribableNullableEntityInterface
{
    public function setDescription(?string $description): void;
    public function getDescription(): ?string;
}
