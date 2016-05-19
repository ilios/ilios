<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Class InstructorGroupManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class InstructorGroupManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findInstructorGroupBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findInstructorGroupsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateInstructorGroup(
        InstructorGroupInterface $instructorGroup,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($instructorGroup, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteInstructorGroup(
        InstructorGroupInterface $instructorGroup
    ) {
        $this->delete($instructorGroup);
    }

    /**
     * @deprecated
     */
    public function createInstructorGroup()
    {
        return $this->create();
    }
}
