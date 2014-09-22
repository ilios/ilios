<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\PermissionInterface;

/**
 * PermissionManager
 */
abstract class PermissionManager implements PermissionManagerInterface
{
    /**
     * @return PermissionInterface
     */
     public function createPermission()
     {
         $class = $this->getClass();

         return new $class();
     }
}
