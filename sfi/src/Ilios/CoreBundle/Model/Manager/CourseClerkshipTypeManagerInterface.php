<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CourseClerkshipTypeInterface;

/**
 * Interface CourseClerkshipTypeManagerInterface
 */
interface CourseClerkshipTypeManagerInterface
{
    /** 
     *@return CourseClerkshipTypeInterface
     */
    public function createCourseClerkshipType();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CourseClerkshipTypeInterface
     */
    public function findCourseClerkshipTypeBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return CourseClerkshipTypeInterface[]|Collection
     */
    public function findCourseClerkshipTypesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCourseClerkshipType(CourseClerkshipTypeInterface $courseClerkshipType, $andFlush = true);

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     *
     * @return void
     */
    public function deleteCourseClerkshipType(CourseClerkshipTypeInterface $courseClerkshipType);

    /**
     * @return string
     */
    public function getClass();
}
