<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\UserInterface;

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
     * Retrieves all courses associated with the given user.
     *
     * @param UserInterface $user
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return CourseInterface[]
     *
     * @see Ilios\CoreBundle\Entity\Repository\CourseRepository::findByUser()
     */
    public function findCoursesByUser(
        UserInterface $user,
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

    /**
     * Checks if a given user is assigned as instructor to ILMs or offerings in a given course.
     *
     * @param UserInterface $user
     * @param CourseInterface $course
     * @return boolean TRUE if the user instructs at least one offering or ILM, FALSE otherwise.
     */
    public function isUserInstructingInCourse(UserInterface $user, CourseInterface $course);
}
