<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\IlmSessionInterface;

/**
 * Class IlmSessionsEntity
 */
trait IlmSessionsEntity
{
    /**
     * @param Collection $ilmSessions
     */
    public function setIlmSessions(Collection $ilmSessions)
    {
        $this->ilmSessions = new ArrayCollection();

        foreach ($ilmSessions as $ilmSession) {
            $this->addIlmSession($ilmSession);
        }
    }

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function addIlmSession(IlmSessionInterface $ilmSession)
    {
        if (!$this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->add($ilmSession);
        }
    }

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function removeIlmSession(IlmSessionInterface $ilmSession)
    {
        $this->ilmSessions->removeElement($ilmSession);
    }

    /**
    * @return IlmSessionInterface[]|ArrayCollection
    */
    public function getIlmSessions()
    {
        return $this->ilmSessions;
    }
}
