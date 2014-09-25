<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CohortInterface;

/**
 * Interface CohortManagerInterface
 */
interface CohortManagerInterface
{
    /** 
     *@return CohortInterface
     */
    public function createCohort();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CohortInterface
     */
    public function findCohortBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CohortInterface[]|Collection
     */
    public function findCohortsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CohortInterface $cohort
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCohort(CohortInterface $cohort, $andFlush = true);

    /**
     * @param CohortInterface $cohort
     *
     * @return void
     */
    public function deleteCohort(CohortInterface $cohort);

    /**
     * @return string
     */
    public function getClass();
}
