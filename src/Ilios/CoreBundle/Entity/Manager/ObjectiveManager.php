<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Class ObjectiveManager
 * @package Ilios\CoreBundle\Entity\Manager
 */

class ObjectiveManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findObjectiveBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findObjectivesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateObjective(
        ObjectiveInterface $objective,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($objective, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteObjective(
        ObjectiveInterface $objective
    ) {
        $this->delete($objective);
    }

    /**
     * @deprecated
     */
    public function createObjective()
    {
        return $this->create();
    }

    /**
     * @return int
     */
    public function getTotalObjectiveCount()
    {
        return $this->em->createQuery('SELECT COUNT(o.id) FROM IliosCoreBundle:Objective o')->getSingleScalarResult();
    }
}
