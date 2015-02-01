<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * InstructorGroup manager service.
 * Class InstructorGroupManager
 * @package Ilios\CoreBundle\Manager
 */
class InstructorGroupManager implements InstructorGroupManagerInterface
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
     * @return InstructorGroupInterface
     */
    public function findInstructorGroupBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return InstructorGroupInterface[]|Collection
     */
    public function findInstructorGroupsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     * @param bool $andFlush
     */
    public function updateInstructorGroup(InstructorGroupInterface $instructorGroup, $andFlush = true)
    {
        $this->em->persist($instructorGroup);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function deleteInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        $this->em->remove($instructorGroup);
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
     * @return InstructorGroupInterface
     */
    public function createInstructorGroup()
    {
        $class = $this->getClass();
        return new $class();
    }
}
