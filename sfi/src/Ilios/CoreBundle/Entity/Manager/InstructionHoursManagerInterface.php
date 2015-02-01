<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\InstructionHoursInterface;

/**
 * Interface InstructionHoursManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface InstructionHoursManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return InstructionHoursInterface
     */
    public function findInstructionHoursBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return InstructionHoursInterface[]|Collection
     */
    public function findAllInstructionHoursBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param InstructionHoursInterface $instructionHours
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateInstructionHours(InstructionHoursInterface $instructionHours, $andFlush = true);

    /**
     * @param InstructionHoursInterface $instructionHours
     *
     * @return void
     */
    public function deleteInstructionHours(InstructionHoursInterface $instructionHours);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return InstructionHoursInterface
     */
    public function createInstructionHours();
}