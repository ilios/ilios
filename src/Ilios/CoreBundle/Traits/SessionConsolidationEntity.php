<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\SessionInterface;

/**
 * Class SessionsEntity
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
