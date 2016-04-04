<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CohortInterface;

/**
 * Interface CohortManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CohortManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CohortInterface
     */
    public function findCohortBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CohortDTO
     */
    public function findCohortDTOBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CohortInterface[]
     */
    public function findCohortsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

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
    );

    /**
     * @param CohortInterface $cohort
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCohort(
        CohortInterface $cohort,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CohortInterface $cohort
     *
     * @return void
     */
    public function deleteCohort(
        CohortInterface $cohort
    );

    /**
     * @return CohortInterface
     */
    public function createCohort();
}
