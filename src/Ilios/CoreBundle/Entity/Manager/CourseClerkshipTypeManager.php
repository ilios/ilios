<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * Class CourseClerkshipTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseClerkshipTypeManager implements CourseClerkshipTypeManagerInterface
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
     * @return CourseClerkshipTypeInterface
     */
    public function findCourseClerkshipTypeBy(
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
     * @return ArrayCollection|CourseClerkshipTypeInterface[]
     */
    public function findCourseClerkshipTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($courseClerkshipType);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($courseClerkshipType));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     */
    public function deleteCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType
    ) {
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

    /**
     * @return CourseClerkshipTypeInterface
     */
    public function createCourseClerkshipType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
