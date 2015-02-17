<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * Interface ProgramManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface ProgramManagerInterface
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
     * @return ProgramInterface[]|Collection
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
     *
     * @return void
     */
    public function updateProgram(
        ProgramInterface $program,
        $andFlush = true
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
     * @return string
     */
    public function getClass();

    /**
     * @return ProgramInterface
     */
    public function createProgram();
}
