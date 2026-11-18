<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\CompetenciesEntityInterface;
use App\Entity\IdentifiableStringEntityInterface;

interface AamcPcrsInterface extends
    IdentifiableStringEntityInterface,
    LoggableEntityInterface,
    CompetenciesEntityInterface
{
    public function setDescription(string $description): void;
    public function getDescription(): string;
}
