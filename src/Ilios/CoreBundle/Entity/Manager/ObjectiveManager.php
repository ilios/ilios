<?php

namespace Ilios\CoreBundle\Entity\Manager;

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
        return $this->em->createQuery('SELECT COUNT(o.id) FROM IliosCoreBundle:Objective o')->getSingleScalarResult();
    }
}
