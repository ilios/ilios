<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\DepartmentManager as BaseDepartmentManager;
use Ilios\CoreBundle\Model\DepartmentInterface;

class DepartmentManager extends BaseDepartmentManager
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
     * @return DepartmentInterface
     */
    public function findDepartmentBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return DepartmentInterface[]|Collection
     */
    public function findDepartmentsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param DepartmentInterface $department
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateDepartment(DepartmentInterface $department, $andFlush = true)
    {
        $this->em->persist($department);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param DepartmentInterface $department
     *
     * @return void
     */
    public function deleteDepartment(DepartmentInterface $department)
    {
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
}
