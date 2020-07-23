<?php

declare(strict_types=1);

namespace App\Entity\Manager;

/**
 * Class SessionDescriptionManager
 * @deprecated
 */
class SessionDescriptionManager extends BaseManager
{
    /**
     * @return int
     */
    public function getTotalSessionDescriptionCount()
    {
        return $this->em->createQuery('SELECT COUNT(s.id) FROM App\Entity\SessionDescription s')
            ->getSingleScalarResult();
    }
}
