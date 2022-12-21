<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableStringEntityInterface;
use App\Traits\CompetenciesEntityInterface;

interface AamcPcrsInterface extends
    IdentifiableStringEntityInterface,
    LoggableEntityInterface,
    CompetenciesEntityInterface
{
    public function setDescription(string $description);
    public function getDescription(): string;
}
