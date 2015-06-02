<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CISessionInterface;

/**
 * Interface CISessionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CISessionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CISessionInterface
     */
    public function findCISessionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CISessionInterface[]
     */
    public function findCISessionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CISessionInterface $cISession
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCISession(
        CISessionInterface $cISession,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CISessionInterface $cISession
     *
     * @return void
     */
    public function deleteCISession(
        CISessionInterface $cISession
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CISessionInterface
     */
    public function createCISession();
}
