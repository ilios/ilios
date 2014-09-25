<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\ObjectiveInterface;

/**
 * Interface ObjectiveManagerInterface
 */
interface ObjectiveManagerInterface
{
    /** 
     *@return ObjectiveInterface
     */
    public function createObjective();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ObjectiveInterface
     */
    public function findObjectiveBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ObjectiveInterface[]|Collection
     */
    public function findObjectivesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ObjectiveInterface $objective
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateObjective(ObjectiveInterface $objective, $andFlush = true);

    /**
     * @param ObjectiveInterface $objective
     *
     * @return void
     */
    public function deleteObjective(ObjectiveInterface $objective);

    /**
     * @return string
     */
    public function getClass();
}
