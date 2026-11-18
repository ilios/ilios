<?php

declare(strict_types=1);

namespace App\Entity;

interface NameableEntityInterface
{
    public function setName(string $name): void;
    public function getName(): string;
}
