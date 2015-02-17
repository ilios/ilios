<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * Interface LearnerGroupManagerInterface
 * @package Ilios\CoreBundle\Manager
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
     * @return LearnerGroupInterface[]|Collection
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
     *
     * @return void
     */
    public function updateLearnerGroup(
        LearnerGroupInterface $learnerGroup,
        $andFlush = true
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
