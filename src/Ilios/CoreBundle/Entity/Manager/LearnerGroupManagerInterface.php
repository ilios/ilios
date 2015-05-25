<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * Interface LearnerGroupManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface LearnerGroupManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearnerGroupInterface
     */
    public function findLearnerGroupBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function findLearnerGroupsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param LearnerGroupInterface $learnerGroup
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateLearnerGroup(
        LearnerGroupInterface $learnerGroup,
        $andFlush = true,
        $forceId  = false
    );

    /**
     * @param LearnerGroupInterface $learnerGroup
     *
     * @return void
     */
    public function deleteLearnerGroup(
        LearnerGroupInterface $learnerGroup
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return LearnerGroupInterface
     */
    public function createLearnerGroup();
}
