<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\NameableEntityInterface;
use AppBundle\Traits\SessionTypesEntityInterface;

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
