<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\InstructionHoursInterface;

/**
 * Class InstructionHoursManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class InstructionHoursManager extends AbstractManager implements InstructionHoursManagerInterface
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
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

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
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param InstructionHoursInterface $instructionHours
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateInstructionHours(
        InstructionHoursInterface $instructionHours,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($instructionHours);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($instructionHours));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param InstructionHoursInterface $instructionHours
     */
    public function deleteInstructionHours(
        InstructionHoursInterface $instructionHours
    ) {
        $this->em->remove($instructionHours);
        $this->em->flush();
    }

    /**
     * @return InstructionHoursInterface
     */
    public function createInstructionHours()
    {
        $class = $this->getClass();
        return new $class();
    }
}
