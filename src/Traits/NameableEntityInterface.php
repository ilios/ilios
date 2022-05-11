<?php

declare(strict_types=1);

namespace App\Traits;

interface NameableEntityInterface
{
    public function setName(string $name);
    public function getName(): string;
}
