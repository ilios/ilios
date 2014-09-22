<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\UserManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

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
