<?php

namespace App\Entity\Manager;

use App\Classes\IndexableCourse;
use App\Entity\CourseInterface;
use App\Entity\Repository\CourseRepository;
use App\Entity\UserInterface;

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
     * @see App\Entity\Repository\CourseRepository::findByUserId()
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
     * Get all the IDs for every course
     *
     * @return array
     * @throws \Exception
     */
    public function getIds()
    {
        /** @var CourseRepository $repository */
        $repository = $this->getRepository();
        return $repository->getIds();
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

    /**
     * Create course index objects for a set of courses
     *
     * @param array $courseIds
     *
     * @return IndexableCourse[]
     */
    public function getCourseIndexesFor(array $courseIds)
    {
        /** @var CourseRepository $repository */
        $repository = $this->getRepository();
        return $repository->getCourseIndexesFor($courseIds);
    }
}
