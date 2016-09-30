<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface SessionDescriptionInterface
 * @package Ilios\CoreBundle\Entity
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
