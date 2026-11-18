<?php

declare(strict_types=1);

namespace App\Entity;

interface TitledNullableEntityInterface
{
    public function setTitle(?string $title): void;
    public function getTitle(): ?string;
}
