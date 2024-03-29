<?php

declare(strict_types=1);

namespace App\Traits;

interface IdentifiableEntityInterface
{
    public function setId(int $id): void;
    public function getId(): int;
}
