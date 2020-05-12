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

    public function findV1DTOBy(array $criteria)
    {
        $results = $this->findV1DTOsBy($criteria, null, 1);
        return empty($results) ? false : $results[0];
    }

    public function findV1DTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findV1DTOsBy($criteria, $orderBy, $limit, $offset);
    }
}
