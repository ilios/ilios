<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CohortInterface;
use Ilios\CoreBundle\Entity\DTO\CohortDTO;

/**
 * Class CohortManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CohortManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findCohortBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CohortDTO
     */
    public function findCohortDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);
        return empty($results)?false:$results[0];
    }

    /**
     * @deprecated
     */
    public function findCohortsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CohortDTO[]
     */
    public function findCohortDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateCohort(
        CohortInterface $cohort,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($cohort, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteCohort(
        CohortInterface $cohort
    ) {
        $this->delete($cohort);
    }

    /**
     * @deprecated
     */
    public function createCohort()
    {
        return $this->create();
    }
}
