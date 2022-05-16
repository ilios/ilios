<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableStringEntityInterface;
use App\Traits\StringableEntityToIdInterface;
use App\Traits\TitledEntityInterface;

interface AamcResourceTypeInterface extends
    IdentifiableStringEntityInterface,
    StringableEntityToIdInterface,
    TitledEntityInterface,
    CategorizableEntityInterface
{
    public function setDescription(string $description);
    public function getDescription(): string;
}
