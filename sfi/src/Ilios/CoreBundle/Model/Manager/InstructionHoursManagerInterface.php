<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\InstructionHoursInterface;

/**
 * Interface InstructionHoursManagerInterface
 */
interface InstructionHoursManagerInterface
{
    /** 
     *@return InstructionHoursInterface
     */
    public function createInstructionHours();

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
    public function findInstructionHoursBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

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
}
