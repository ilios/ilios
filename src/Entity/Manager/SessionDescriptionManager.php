<?php

namespace App\Entity\Manager;

/**
 * Class SessionDescriptionManager
 */
class SessionDescriptionManager extends BaseManager
{
    /**
     * @return int
     */
    public function getTotalSessionDescriptionCount()
    {
        return $this->em->createQuery('SELECT COUNT(s.id) FROM AppBundle:SessionDescription s')
            ->getSingleScalarResult();
    }
}
