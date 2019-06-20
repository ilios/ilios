<?php

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SessionTypesEntityInterface;

/**
 * Interface AamcMethodInterface
 */
interface AamcMethodInterface extends
    IdentifiableEntityInterface,
    DescribableEntityInterface,
    LoggableEntityInterface,
    SessionTypesEntityInterface,
    ActivatableEntityInterface
{
}
