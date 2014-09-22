<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\UserSyncExceptionManagerInterface;
use Ilios\CoreBundle\Entity\UserSyncExceptionInterface;

/**
 * UserSyncExceptionManager
 */
abstract class UserSyncExceptionManager implements UserSyncExceptionManagerInterface
{
    /**
     * @return UserSyncExceptionInterface
     */
     public function createUserSyncException()
     {
         $class = $this->getClass();

         return new $class();
     }
}
