<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

/**
 * Interface ProgramYearStewardManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface ProgramYearStewardManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramYearStewardInterface
     */
    public function findProgramYearStewardBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|ProgramYearStewardInterface[]
     */
    public function findProgramYearStewardsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param ProgramYearStewardInterface $programYearSteward
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateProgramYearSteward(
        ProgramYearStewardInterface $programYearSteward,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param ProgramYearStewardInterface $programYearSteward
     *
     * @return void
     */
    public function deleteProgramYearSteward(
        ProgramYearStewardInterface $programYearSteward
    );

    /**
     * @return ProgramYearStewardInterface
     */
    public function createProgramYearSteward();
}
