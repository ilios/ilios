<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableStringEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\TitledEntityInterface;

interface AamcResourceTypeInterface extends
    IdentifiableStringEntityInterface,
    StringableEntityInterface,
    TitledEntityInterface,
    CategorizableEntityInterface
{
    public function setDescription(string $description);
    public function getDescription(): string;
}
