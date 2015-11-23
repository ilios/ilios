<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * Interface SessionDescriptionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface SessionDescriptionManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionDescriptionInterface
     */
    public function findSessionDescriptionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionDescriptionInterface[]
     */
    public function findSessionDescriptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param SessionDescriptionInterface $sessionDescription
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateSessionDescription(
        SessionDescriptionInterface $sessionDescription,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param SessionDescriptionInterface $sessionDescription
     *
     * @return void
     */
    public function deleteSessionDescription(
        SessionDescriptionInterface $sessionDescription
    );

    /**
     * @return SessionDescriptionInterface
     */
    public function createSessionDescription();

    /**
     * @return integer
     */
    public function getTotalSessionDescriptionCount();
}
