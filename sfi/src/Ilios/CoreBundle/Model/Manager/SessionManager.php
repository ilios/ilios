<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\SessionManagerInterface;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * SessionManager
 */
abstract class SessionManager implements SessionManagerInterface
{
    /**
     * @return SessionInterface
     */
     public function createSession()
     {
         $class = $this->getClass();

         return new $class();
     }
}
