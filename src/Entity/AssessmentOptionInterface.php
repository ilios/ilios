<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Traits\SessionTypesEntityInterface;

/**
 * Interface AssessmentOptionInterface
 */
interface AssessmentOptionInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    LoggableEntityInterface,
    SessionTypesEntityInterface
{
}
