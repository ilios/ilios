<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;

/**
 * Interface AssessmentOptionInterface
 */
interface AssessmentOptionInterface  extends IdentifiableEntityInterface, NameableEntityInterface
{

    /**
     * @param Collection $sessionTypes
     */
    public function setSessionTypes(Collection $sessionTypes);

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function addSessionType(SessionTypeInterface $sessionType);

    /**
     * @return ArrayCollection|SessionTypeInterface[]
     */
    public function getSessionTypes();
}

