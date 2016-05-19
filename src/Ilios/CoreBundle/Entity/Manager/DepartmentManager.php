<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Class DepartmentManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class DepartmentManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findDepartmentBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findDepartmentsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateDepartment(
        DepartmentInterface $department,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($department, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteDepartment(
        DepartmentInterface $department
    ) {
        $this->delete($department);
    }

    /**
     * @deprecated
     */
    public function createDepartment()
    {
        return $this->create();
    }
}
