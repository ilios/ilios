<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Interface InstructorGroupManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface InstructorGroupManagerInterface
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
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return InstructorGroupInterface[]|Collection
     */
    public function findInstructorGroupsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param InstructorGroupInterface $instructorGroup
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateInstructorGroup(
        InstructorGroupInterface $instructorGroup,
        $andFlush = true
    );

    /**
     * @param InstructorGroupInterface $instructorGroup
     *
     * @return void
     */
    public function deleteInstructorGroup(
        InstructorGroupInterface $instructorGroup
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return InstructorGroupInterface
     */
    public function createInstructorGroup();
}
