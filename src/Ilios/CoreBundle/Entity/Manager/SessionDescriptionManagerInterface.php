<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * Interface SessionDescriptionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface SessionDescriptionManagerInterface
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
     * @return ArrayCollection|SessionDescriptionInterface[]
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
        $forceId  = false
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
     * @return string
     */
    public function getClass();

    /**
     * @return SessionDescriptionInterface
     */
    public function createSessionDescription();
}
