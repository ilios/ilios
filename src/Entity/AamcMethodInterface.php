<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\IdentifiableStringEntityInterface;
use App\Traits\SessionTypesEntityInterface;
use Stringable;

interface AamcMethodInterface extends
    IdentifiableStringEntityInterface,
    LoggableEntityInterface,
    SessionTypesEntityInterface,
    ActivatableEntityInterface,
    Stringable
{
    public function setDescription(string $description);
    public function getDescription(): string;
}
