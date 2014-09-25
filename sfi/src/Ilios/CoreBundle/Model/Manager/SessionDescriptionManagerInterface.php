<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\SessionDescriptionInterface;

/**
 * Interface SessionDescriptionManagerInterface
 */
interface SessionDescriptionManagerInterface
{
    /** 
     *@return SessionDescriptionInterface
     */
    public function createSessionDescription();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionDescriptionInterface
     */
    public function findSessionDescriptionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionDescriptionInterface[]|Collection
     */
    public function findSessionDescriptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param SessionDescriptionInterface $sessionDescription
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateSessionDescription(SessionDescriptionInterface $sessionDescription, $andFlush = true);

    /**
     * @param SessionDescriptionInterface $sessionDescription
     *
     * @return void
     */
    public function deleteSessionDescription(SessionDescriptionInterface $sessionDescription);

    /**
     * @return string
     */
    public function getClass();
}
