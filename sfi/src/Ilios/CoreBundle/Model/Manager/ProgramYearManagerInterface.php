<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\ProgramYearInterface;

/**
 * Interface ProgramYearManagerInterface
 */
interface ProgramYearManagerInterface
{
    /** 
     *@return ProgramYearInterface
     */
    public function createProgramYear();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramYearInterface
     */
    public function findProgramYearBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ProgramYearInterface[]|Collection
     */
    public function findProgramYearsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ProgramYearInterface $programYear
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateProgramYear(ProgramYearInterface $programYear, $andFlush = true);

    /**
     * @param ProgramYearInterface $programYear
     *
     * @return void
     */
    public function deleteProgramYear(ProgramYearInterface $programYear);

    /**
     * @return string
     */
    public function getClass();
}
