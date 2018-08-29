<?php

namespace AppBundle\Entity\Manager;

/**
 * Class ObjectiveManager
 */

class ObjectiveManager extends BaseManager
{
    /**
     * @return int
     */
    public function getTotalObjectiveCount()
    {
        return $this->em->createQuery('SELECT COUNT(o.id) FROM AppBundle:Objective o')->getSingleScalarResult();
    }
}
