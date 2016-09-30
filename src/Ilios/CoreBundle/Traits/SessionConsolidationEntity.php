<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Class SessionsEntity
 * @package Ilios\CoreBundle\Traits
 */
trait SessionConsolidationEntity
{

    /**
    * @return SessionInterface[]|ArrayCollection
    */
    public function getSessions()
    {
        $session = $this->getSession();
        if ($session) {
            return [$session];
        }

        return [];
    }
}
