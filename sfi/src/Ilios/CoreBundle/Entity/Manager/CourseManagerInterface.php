<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Interface CourseManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface CourseManagerInterface
{
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
     * @param integer $limit
     * @param integer $offset
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

    /**
     * @return CourseInterface
     */
    public function createCourse();
}
