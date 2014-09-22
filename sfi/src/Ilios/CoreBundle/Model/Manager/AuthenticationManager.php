<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;
use Ilios\CoreBundle\Entity\AuthenticationInterface;

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
