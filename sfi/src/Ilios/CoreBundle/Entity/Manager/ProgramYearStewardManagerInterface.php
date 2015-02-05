<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

/**
 * Interface ProgramYearStewardManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface ProgramYearStewardManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramYearStewardInterface
     */
    public function findProgramYearStewardBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ProgramYearStewardInterface[]|Collection
     */
    public function findProgramYearStewardsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ProgramYearStewardInterface $programYearSteward
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateProgramYearSteward(ProgramYearStewardInterface $programYearSteward, $andFlush = true);

    /**
     * @param ProgramYearStewardInterface $programYearSteward
     *
     * @return void
     */
    public function deleteProgramYearSteward(ProgramYearStewardInterface $programYearSteward);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return ProgramYearStewardInterface
     */
    public function createProgramYearSteward();
}
