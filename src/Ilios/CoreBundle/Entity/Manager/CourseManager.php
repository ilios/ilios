<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CourseManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseManager extends DTOManager
{
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
    ) {
        return $this->getRepository()->findByUser($user, $criteria, $orderBy, $limit, $offset);
    }

    /**
     * @return string[]
     */
    public function getYears()
    {
        return $this->getRepository()->getYears();
    }

    /**
     * Checks if a given user is assigned as instructor to ILMs or offerings in a given course.
     *
     * @param UserInterface $user
     * @param int $courseId
     * @return boolean TRUE if the user instructs at least one offering or ILM, FALSE otherwise.
     */
    public function isUserInstructingInCourse(UserInterface $user, $courseId)
    {
        return $this->getRepository()->isUserInstructingInCourse($user, $courseId);
    }
}
