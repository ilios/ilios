<?php

namespace AppBundle\Entity;

use AppBundle\Traits\DescribableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface SessionDescriptionInterface
 */
interface SessionDescriptionInterface extends
    DescribableEntityInterface,
    IdentifiableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface
{
    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface|null
     */
    public function getSession();
}
