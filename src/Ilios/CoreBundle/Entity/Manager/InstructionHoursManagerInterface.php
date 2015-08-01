<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\InstructionHoursInterface;

/**
 * Interface InstructionHoursManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface InstructionHoursManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return InstructionHoursInterface
     */
    public function findInstructionHoursBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|InstructionHoursInterface[]
     */
    public function findInstructionHoursesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param InstructionHoursInterface $instructionHours
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateInstructionHours(
        InstructionHoursInterface $instructionHours,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param InstructionHoursInterface $instructionHours
     *
     * @return void
     */
    public function deleteInstructionHours(
        InstructionHoursInterface $instructionHours
    );

    /**
     * @return InstructionHoursInterface
     */
    public function createInstructionHours();
}
