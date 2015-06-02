<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Interface SessionTypeManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface SessionTypeManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionTypeInterface
     */
    public function findSessionTypeBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|SessionTypeInterface[]
     */
    public function findSessionTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param SessionTypeInterface $sessionType
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateSessionType(
        SessionTypeInterface $sessionType,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param SessionTypeInterface $sessionType
     *
     * @return void
     */
    public function deleteSessionType(
        SessionTypeInterface $sessionType
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return SessionTypeInterface
     */
    public function createSessionType();
}
