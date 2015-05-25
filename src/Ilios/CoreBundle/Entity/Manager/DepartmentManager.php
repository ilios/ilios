<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Class DepartmentManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class DepartmentManager implements DepartmentManagerInterface
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
     * @return DepartmentInterface
     */
    public function findDepartmentBy(
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
     * @return ArrayCollection|DepartmentInterface[]
     */
    public function findDepartmentsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param DepartmentInterface $department
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateDepartment(
        DepartmentInterface $department,
        $andFlush = true,
        $forceId  = false
    ) {
        $this->em->persist($department);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($department));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param DepartmentInterface $department
     */
    public function deleteDepartment(
        DepartmentInterface $department
    ) {
        $this->em->remove($department);
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
     * @return DepartmentInterface
     */
    public function createDepartment()
    {
        $class = $this->getClass();
        return new $class();
    }
}
