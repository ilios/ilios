<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\SessionTypeManagerInterface;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * SessionTypeManager
 */
abstract class SessionTypeManager implements SessionTypeManagerInterface
{
    /**
     * @return SessionTypeInterface
     */
     public function createSessionType()
     {
         $class = $this->getClass();

         return new $class();
     }
}
