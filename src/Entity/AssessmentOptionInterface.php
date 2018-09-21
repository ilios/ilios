<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

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
