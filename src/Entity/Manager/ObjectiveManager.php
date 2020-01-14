<?php

declare(strict_types=1);

namespace App\Entity\Manager;

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
        return $this->em->createQuery('SELECT COUNT(o.id) FROM App\Entity\Objective o')->getSingleScalarResult();
    }
}
