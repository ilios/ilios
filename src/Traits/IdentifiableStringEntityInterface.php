<?php

declare(strict_types=1);

namespace App\Traits;

interface IdentifiableStringEntityInterface
{
    public function setId(string $id);

    public function getId(): string;
}
