<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * Interface ProgramYearManagerInterface
 * @package Ilios\CoreBundle\Manager
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
     * @return ProgramYearInterface[]|Collection
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
     *
     * @return void
     */
    public function updateProgramYear(
        ProgramYearInterface $programYear,
        $andFlush = true
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
