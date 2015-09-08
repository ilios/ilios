<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Class InstructorGroupManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class InstructorGroupManager extends AbstractManager implements InstructorGroupManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return InstructorGroupInterface
     */
    public function findInstructorGroupBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function findInstructorGroupsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateInstructorGroup(
        InstructorGroupInterface $instructorGroup,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($instructorGroup);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($instructorGroup));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function deleteInstructorGroup(
        InstructorGroupInterface $instructorGroup
    ) {
        $this->em->remove($instructorGroup);
        $this->em->flush();
    }

    /**
     * @return InstructorGroupInterface
     */
    public function createInstructorGroup()
    {
        $class = $this->getClass();
        return new $class();
    }
}
