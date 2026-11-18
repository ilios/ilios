<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableStringEntityInterface;
use App\Entity\CompetenciesEntityInterface;

interface AamcPcrsInterface extends
    IdentifiableStringEntityInterface,
    LoggableEntityInterface,
    CompetenciesEntityInterface
{
    public function setDescription(string $description): void;
    public function getDescription(): string;
}
