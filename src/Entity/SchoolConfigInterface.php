<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Traits\SchoolEntityInterface;
use Stringable;

interface SchoolConfigInterface extends
    SchoolEntityInterface,
    NameableEntityInterface,
    IdentifiableEntityInterface,
    Stringable
{
    public function getValue(): string;
    public function setValue(string $value);
}
