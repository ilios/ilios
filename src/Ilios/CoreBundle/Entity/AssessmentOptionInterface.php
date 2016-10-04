<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\SessionTypesEntityInterface;

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
