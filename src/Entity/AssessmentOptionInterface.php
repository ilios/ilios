<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableEntityInterface;
use App\Entity\NameableEntityInterface;
use App\Entity\SessionTypesEntityInterface;

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
