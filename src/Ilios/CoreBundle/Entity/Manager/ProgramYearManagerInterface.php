<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * Interface ProgramYearManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface ProgramYearManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramYearInterface
     */
    public function findProgramYearBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|ProgramYearInterface[]
     */
    public function findProgramYearsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param ProgramYearInterface $programYear
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateProgramYear(
        ProgramYearInterface $programYear,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param ProgramYearInterface $programYear
     *
     * @return void
     */
    public function deleteProgramYear(
        ProgramYearInterface $programYear
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return ProgramYearInterface
     */
    public function createProgramYear();
}
