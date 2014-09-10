<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\UserInterface;

/**
 * Class UserManager
 * @package Ilios\CoreBundle\Model\Manager
 * @author Victor Passapera <vpassapera@gmail.com>
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
