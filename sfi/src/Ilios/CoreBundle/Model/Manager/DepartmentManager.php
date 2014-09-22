<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\DepartmentManagerInterface;
use Ilios\CoreBundle\Entity\DepartmentInterface;

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
