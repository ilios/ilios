<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CohortInterface;

/**
 * Class CohortManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CohortManager extends DTOManager
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
     * @deprecated
     */
    public function findCohortDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findDTOBy($criteria, $orderBy);
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
     * @deprecated
     */
    public function findCohortDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findDTOsBy($criteria, $orderBy, $limit, $offset);
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
