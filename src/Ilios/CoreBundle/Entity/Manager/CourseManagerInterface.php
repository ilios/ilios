<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Interface CourseManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CourseManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CourseInterface
     */
    public function findCourseBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CourseInterface[]
     */
    public function findCoursesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CourseInterface $course
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCourse(
        CourseInterface $course,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CourseInterface $course
     *
     * @return void
     */
    public function deleteCourse(
        CourseInterface $course
    );

    /**
     * @return string[]
     */
    public function getYears();

    /**
     * @return CourseInterface
     */
    public function createCourse();
}
