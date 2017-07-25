<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Interface SessionsEntityInterface
 */
interface SessionsEntityInterface
{
    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions);

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session);

    /**
     * @param SessionInterface $session
     */
    public function removeSession(SessionInterface $session);

    /**
    * @return SessionInterface[]|ArrayCollection
    */
    public function getSessions();
}
