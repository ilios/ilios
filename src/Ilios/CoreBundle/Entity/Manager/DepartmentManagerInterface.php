<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Interface DepartmentManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface DepartmentManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return DepartmentInterface
     */
    public function findDepartmentBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return DepartmentInterface[]
     */
    public function findDepartmentsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param DepartmentInterface $department
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateDepartment(
        DepartmentInterface $department,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param DepartmentInterface $department
     *
     * @return void
     */
    public function deleteDepartment(
        DepartmentInterface $department
    );

    /**
     * @return DepartmentInterface
     */
    public function createDepartment();
}
