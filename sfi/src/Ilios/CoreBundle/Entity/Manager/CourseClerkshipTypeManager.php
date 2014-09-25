<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CourseClerkshipTypeManager as BaseCourseClerkshipTypeManager;
use Ilios\CoreBundle\Model\CourseClerkshipTypeInterface;

class CourseClerkshipTypeManager extends BaseCourseClerkshipTypeManager
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
     * @return CourseClerkshipTypeInterface
     */
    public function findCourseClerkshipTypeBy(array $criteria, array $orderBy = null)
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
     * @return CourseClerkshipTypeInterface[]|Collection
     */
    public function findCourseClerkshipTypesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCourseClerkshipType(CourseClerkshipTypeInterface $courseClerkshipType, $andFlush = true)
    {
        $this->em->persist($courseClerkshipType);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     *
     * @return void
     */
    public function deleteCourseClerkshipType(CourseClerkshipTypeInterface $courseClerkshipType)
    {
        $this->em->remove($courseClerkshipType);
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
