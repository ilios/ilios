<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Interface CourseManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CourseManagerInterface
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
     * @return ArrayCollection|CourseInterface[]
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
     * @return string
     */
    public function getClass();

    /**
     * @return CourseInterface
     */
    public function createCourse();
}
