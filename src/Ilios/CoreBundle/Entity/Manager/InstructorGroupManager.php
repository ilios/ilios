<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Class InstructorGroupManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class InstructorGroupManager extends BaseManager implements InstructorGroupManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findInstructorGroupBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deleteInstructorGroup(
        InstructorGroupInterface $instructorGroup
    ) {
        $this->em->remove($instructorGroup);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createInstructorGroup()
    {
        $class = $this->getClass();
        return new $class();
    }
}
