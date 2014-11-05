<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CourseInterface;

/**
 * Interface CourseManagerInterface
 */
interface CourseManagerInterface
{
    /** 
     *@return CourseInterface
     */
    public function createCourse();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CourseInterface
     */
    public function findCourseBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return CourseInterface[]|Collection
     */
    public function findCoursesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CourseInterface $course
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCourse(CourseInterface $course, $andFlush = true);

    /**
     * @param CourseInterface $course
     *
     * @return void
     */
    public function deleteCourse(CourseInterface $course);

    /**
     * @return string
     */
    public function getClass();
}
