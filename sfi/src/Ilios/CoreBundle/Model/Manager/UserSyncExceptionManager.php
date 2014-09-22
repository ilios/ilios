<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\UserSyncExceptionManagerInterface;
use Ilios\CoreBundle\Model\UserSyncExceptionInterface;

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
