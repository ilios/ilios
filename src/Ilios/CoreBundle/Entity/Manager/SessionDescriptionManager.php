<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * Class SessionDescriptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionDescriptionManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findSessionDescriptionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findSessionDescriptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateSessionDescription(
        SessionDescriptionInterface $sessionDescription,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($sessionDescription, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteSessionDescription(
        SessionDescriptionInterface $sessionDescription
    ) {
        $this->delete($sessionDescription);
    }

    /**
     * @deprecated
     */
    public function createSessionDescription()
    {
        return $this->create();
    }

    /**
     * @return int
     */
    public function getTotalSessionDescriptionCount()
    {
        return $this->em->createQuery('SELECT COUNT(s.id) FROM IliosCoreBundle:SessionDescription s')
            ->getSingleScalarResult();
    }
}
