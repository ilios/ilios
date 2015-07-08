<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Class CourseManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseManager extends AbstractManager implements CourseManagerInterface
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
    ) {
        $criteria['deleted'] = false;
        return $this->repository->findOneBy($criteria, $orderBy);
    }

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
    ) {
        $criteria['deleted'] = false;
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CourseInterface $course
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCourse(
        CourseInterface $course,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($course);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($course));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CourseInterface $course
     */
    public function deleteCourse(
        CourseInterface $course
    ) {
        $course->setDeleted(true);
        $this->updateCourse($course);
    }

    /**
     * @return string[]
     */
    public function getYears()
    {
        return $this->repository->getYears();
    }

    /**
     * @return CourseInterface
     */
    public function createCourse()
    {
        $class = $this->getClass();
        return new $class();
    }
}
