<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Class CourseManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
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
     * @return ArrayCollection|CourseInterface[]
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
     * @param bool $forceId
     */
    public function updateCourse(
        CourseInterface $course,
        $andFlush = true,
        $forceId  = false
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
        $this->em->remove($course);
        $this->em->flush();
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
