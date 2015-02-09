<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Course manager service.
 * Class CourseManager
 * @package Ilios\CoreBundle\Manager
 */
class CourseManager implements CourseManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

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
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CourseInterface[]|Collection
     */
    public function findCoursesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CourseInterface $course
     * @param bool $andFlush
     */
    public function updateCourse(
        CourseInterface $course,
        $andFlush = true
    ) {
        $this->em->persist($course);
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
        $this->em->remove($course);
        $this->em->flush();
    }

    /**
     * @return string[]
     */
    public function getYears()
    {
        return $this->repository->getYears();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
