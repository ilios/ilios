<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Interface ObjectiveManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface ObjectiveManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ObjectiveInterface
     */
    public function findObjectiveBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function findObjectivesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param ObjectiveInterface $objective
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateObjective(
        ObjectiveInterface $objective,
        $andFlush = true,
        $forceId  = false
    );

    /**
     * @param ObjectiveInterface $objective
     *
     * @return void
     */
    public function deleteObjective(
        ObjectiveInterface $objective
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return ObjectiveInterface
     */
    public function createObjective();
}
