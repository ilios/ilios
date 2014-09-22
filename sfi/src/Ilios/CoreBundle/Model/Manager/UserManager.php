<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\UserManagerInterface;
use Ilios\CoreBundle\Model\UserInterface;

/**
 * UserManager
 */
abstract class UserManager implements UserManagerInterface
{
    /**
    * @return UserInterface
    */
    public function createUser()
    {
        $class = $this->getClass();

        return new $class();
    }
}
