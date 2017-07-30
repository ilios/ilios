<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\Repository\CourseRepository;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CourseManager
 */
class CourseManager extends BaseManager
{
    /**
     * Retrieves all courses associated with the given user.
     *
     * @param integer $user
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return CourseInterface[]
     *
     * @see Ilios\CoreBundle\Entity\Repository\CourseRepository::findByUserId()
     */
    public function findCoursesByUserId(
        $userId,
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        /** @var CourseRepository $repository */
        $repository = $this->getRepository();
        return $repository->findByUserId($userId, $criteria, $orderBy, $limit, $offset);
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
     * @param int $userId
     * @param int $courseId
     * @return boolean TRUE if the user instructs at least one offering or ILM, FALSE otherwise.
     */
    public function isUserInstructingInCourse($userId, $courseId)
    {
        /** @var CourseRepository $repository */
        $repository = $this->getRepository();
        return $repository->isUserInstructingInCourse($userId, $courseId);
    }
}
