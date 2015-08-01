<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * Interface CourseClerkshipTypeManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CourseClerkshipTypeManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CourseClerkshipTypeInterface
     */
    public function findCourseClerkshipTypeBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CourseClerkshipTypeInterface[]
     */
    public function findCourseClerkshipTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     *
     * @return void
     */
    public function deleteCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType
    );

    /**
     * @return CourseClerkshipTypeInterface
     */
    public function createCourseClerkshipType();
}
