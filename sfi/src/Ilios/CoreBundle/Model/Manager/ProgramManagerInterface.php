<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\ProgramInterface;

/**
 * Interface ProgramManagerInterface
 */
interface ProgramManagerInterface
{
    /** 
     *@return ProgramInterface
     */
    public function createProgram();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramInterface
     */
    public function findProgramBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return ProgramInterface[]|Collection
     */
    public function findProgramsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ProgramInterface $program
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateProgram(ProgramInterface $program, $andFlush = true);

    /**
     * @param ProgramInterface $program
     *
     * @return void
     */
    public function deleteProgram(ProgramInterface $program);

    /**
     * @return string
     */
    public function getClass();
}
