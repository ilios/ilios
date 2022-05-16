<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableStringEntityInterface;
use App\Traits\CompetenciesEntityInterface;
use App\Traits\StringableEntityToIdInterface;

interface AamcPcrsInterface extends
    IdentifiableStringEntityInterface,
    StringableEntityToIdInterface,
    LoggableEntityInterface,
    CompetenciesEntityInterface
{
    public function setDescription(string $description);
    public function getDescription(): string;
}
