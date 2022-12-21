<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableStringEntityInterface;
use App\Traits\CompetenciesEntityInterface;
use App\Traits\StringableEntityInterface;

interface AamcPcrsInterface extends
    IdentifiableStringEntityInterface,
    StringableEntityInterface,
    LoggableEntityInterface,
    CompetenciesEntityInterface
{
    public function setDescription(string $description);
    public function getDescription(): string;
}
