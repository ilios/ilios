<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\InstructorGroupInterface;

/**
 * Interface InstructorGroupManagerInterface
 */
interface InstructorGroupManagerInterface
{
    /** 
     *@return InstructorGroupInterface
     */
    public function createInstructorGroup();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return InstructorGroupInterface
     */
    public function findInstructorGroupBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return InstructorGroupInterface[]|Collection
     */
    public function findInstructorGroupsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param InstructorGroupInterface $instructorGroup
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateInstructorGroup(InstructorGroupInterface $instructorGroup, $andFlush = true);

    /**
     * @param InstructorGroupInterface $instructorGroup
     *
     * @return void
     */
    public function deleteInstructorGroup(InstructorGroupInterface $instructorGroup);

    /**
     * @return string
     */
    public function getClass();
}
