<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Interface DepartmentManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface DepartmentManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return DepartmentInterface
     */
    public function findDepartmentBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return DepartmentInterface[]|Collection
     */
    public function findDepartmentsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param DepartmentInterface $department
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateDepartment(DepartmentInterface $department, $andFlush = true);

    /**
     * @param DepartmentInterface $department
     *
     * @return void
     */
    public function deleteDepartment(DepartmentInterface $department);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return DepartmentInterface
     */
    public function createDepartment();
}
