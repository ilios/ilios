<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class SessionTypesEntity
 */
trait SessionTypesEntity
{
    /**
     * @param Collection $sessionTypes
     */
    public function setSessionTypes(Collection $sessionTypes)
    {
        $this->sessionTypes = new ArrayCollection();

        foreach ($sessionTypes as $sessionType) {
            $this->addSessionType($sessionType);
        }
    }

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function addSessionType(SessionTypeInterface $sessionType)
    {
        if (!$this->sessionTypes->contains($sessionType)) {
            $this->sessionTypes->add($sessionType);
        }
    }

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function removeSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionTypes->removeElement($sessionType);
    }

    /**
    * @return SessionTypeInterface[]|ArrayCollection
    */
    public function getSessionTypes()
    {
        return $this->sessionTypes;
    }
}
