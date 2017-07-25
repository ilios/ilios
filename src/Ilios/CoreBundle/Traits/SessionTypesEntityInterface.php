<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Interface SessionTypesEntityInterface
 */
interface SessionTypesEntityInterface
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
     * @param SessionTypeInterface $sessionType
     */
    public function removeSessionType(SessionTypeInterface $sessionType);

    /**
    * @return SessionTypeInterface[]|ArrayCollection
    */
    public function getSessionTypes();
}
