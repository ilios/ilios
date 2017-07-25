<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Class SessionsEntity
 */
trait SessionsEntity
{
    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions)
    {
        $this->sessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addSession($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function removeSession(SessionInterface $session)
    {
        $this->sessions->removeElement($session);
    }

    /**
    * @return SessionInterface[]|ArrayCollection
    */
    public function getSessions()
    {
        return $this->sessions;
    }
}
