<?php

namespace Ilios\CoreBundle\Entity;
use Ilios\CoreBundle\Traits\DescribableEntityInterface;

/**
 * Interface SessionDescriptionInterface
 * @package Ilios\CoreBundle\Entity
 */
interface SessionDescriptionInterface extends DescribableEntityInterface
{
    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface
     */
    public function getSession();
}
