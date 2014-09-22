<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\AuthenticationManagerInterface;
use Ilios\CoreBundle\Model\AuthenticationInterface;

/**
 * AuthenticationManager
 */
abstract class AuthenticationManager implements AuthenticationManagerInterface
{
    /**
    * @return AuthenticationInterface
    */
    public function createAuthentication()
    {
        $class = $this->getClass();

        return new $class();
    }
}
