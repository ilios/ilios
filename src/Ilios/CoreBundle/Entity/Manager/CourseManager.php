<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CourseManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseManager extends BaseManager implements CourseManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCourseBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCourseDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);

        return empty($results)?false:$results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findCoursesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findCourseDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deleteCourse(
        CourseInterface $course
    ) {
        $this->em->remove($course);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getYears()
    {
        return $this->getRepository()->getYears();
    }

    /**
     * {@inheritdoc}
     */
    public function createCourse()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * @inheritdoc
     */
    public function isUserInstructingInCourse(UserInterface $user, $courseId)
    {
        return $this->getRepository()->isUserInstructingInCourse($user, $courseId);
    }
}
