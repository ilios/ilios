<?php

declare(strict_types=1);

namespace App\Traits;

interface TitledNullableEntityInterface
{
    public function setTitle(?string $title);
    public function getTitle(): ?string;
}
