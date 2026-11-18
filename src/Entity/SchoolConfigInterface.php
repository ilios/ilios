<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableEntityInterface;
use App\Entity\NameableEntityInterface;
use App\Entity\SchoolEntityInterface;
use Stringable;

interface SchoolConfigInterface extends
    SchoolEntityInterface,
    NameableEntityInterface,
    IdentifiableEntityInterface,
    Stringable
{
    public function getValue(): string;
    public function setValue(string $value): void;
}
