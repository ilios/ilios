<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\DepartmentInterface;

/**
 * Interface DepartmentManagerInterface
 */
interface DepartmentManagerInterface
{
    /** 
     *@return DepartmentInterface
     */
    public function createDepartment();

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
     * @param int $limit
     * @param int $offset
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
}
