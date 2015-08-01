<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * Interface ProgramManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface ProgramManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramInterface
     */
    public function findProgramBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|ProgramInterface[]
     */
    public function findProgramsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param ProgramInterface $program
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateProgram(
        ProgramInterface $program,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param ProgramInterface $program
     *
     * @return void
     */
    public function deleteProgram(
        ProgramInterface $program
    );

    /**
     * @return ProgramInterface
     */
    public function createProgram();
}
