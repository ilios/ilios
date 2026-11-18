<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ActivatableEntityInterface;
use App\Entity\IdentifiableStringEntityInterface;
use App\Entity\SessionTypesEntityInterface;

interface AamcMethodInterface extends
    IdentifiableStringEntityInterface,
    LoggableEntityInterface,
    SessionTypesEntityInterface,
    ActivatableEntityInterface
{
    public function setDescription(string $description): void;
    public function getDescription(): string;
}
