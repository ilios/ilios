<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Interface ObjectiveManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface ObjectiveManagerInterface extends ManagerInterface
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
     * @return ObjectiveInterface[]
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
        $forceId = false
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
     * @return ObjectiveInterface
     */
    public function createObjective();

    /**
     * @return integer
     */
    public function getTotalObjectiveCount();
}
