<?php

namespace Ilios\CoreBundle\Entity\Manager;

/**
 * Class SessionDescriptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionDescriptionManager extends BaseManager
{
    /**
     * @return int
     */
    public function getTotalSessionDescriptionCount()
    {
        return $this->em->createQuery('SELECT COUNT(s.id) FROM IliosCoreBundle:SessionDescription s')
            ->getSingleScalarResult();
    }
}
