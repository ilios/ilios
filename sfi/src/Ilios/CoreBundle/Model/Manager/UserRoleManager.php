<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\UserRoleManagerInterface;
use Ilios\CoreBundle\Entity\UserRoleInterface;

/**
 * UserRoleManager
 */
abstract class UserRoleManager implements UserRoleManagerInterface
{
    /**
     * @return UserRoleInterface
     */
     public function createUserRole()
     {
         $class = $this->getClass();

         return new $class();
     }
}
