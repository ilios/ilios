<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CourseManager as BaseCourseManager;
use Ilios\CoreBundle\Model\CourseInterface;

class CourseManager extends BaseCourseManager
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
    public function findCourseBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CourseInterface[]|Collection
     */
    public function findCoursesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CourseInterface $course
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCourse(CourseInterface $course, $andFlush = true)
    {
        $this->em->persist($course);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CourseInterface $course
     *
     * @return void
     */
    public function deleteCourse(CourseInterface $course)
    {
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
}
