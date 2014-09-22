<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\UserRoleManagerInterface;
use Ilios\CoreBundle\Model\UserRoleInterface;

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
