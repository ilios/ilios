<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableStringEntityInterface;
use App\Traits\TitledEntityInterface;

interface AamcResourceTypeInterface extends
    IdentifiableStringEntityInterface,
    TitledEntityInterface,
    CategorizableEntityInterface
{
    public function setDescription(string $description): void;
    public function getDescription(): string;
}
