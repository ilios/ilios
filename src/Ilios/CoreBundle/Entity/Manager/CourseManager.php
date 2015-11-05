<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CourseManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseManager extends AbstractManager implements CourseManagerInterface
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
    public function isUserInstructingInCourse(UserInterface $user, CourseInterface $course)
    {
        return $this->getRepository()->isUserInstructingInCourse($user, $course);
    }
}
