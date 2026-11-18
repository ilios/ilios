<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableEntityInterface;
use App\Entity\NameableEntityInterface;
use Stringable;

interface ApplicationConfigInterface extends IdentifiableEntityInterface, NameableEntityInterface, Stringable
{
    public function getValue(): string;
    public function setValue(string $value): void;
}
