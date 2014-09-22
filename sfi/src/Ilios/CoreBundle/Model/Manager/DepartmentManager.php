<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\DepartmentManagerInterface;
use Ilios\CoreBundle\Model\DepartmentInterface;

/**
 * DepartmentManager
 */
abstract class DepartmentManager implements DepartmentManagerInterface
{
    /**
    * @return DepartmentInterface
    */
    public function createDepartment()
    {
        $class = $this->getClass();

        return new $class();
    }
}
