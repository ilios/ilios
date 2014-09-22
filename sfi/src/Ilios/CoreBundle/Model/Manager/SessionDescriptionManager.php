<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\SessionDescriptionManagerInterface;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * SessionDescriptionManager
 */
abstract class SessionDescriptionManager implements SessionDescriptionManagerInterface
{
    /**
     * @return SessionDescriptionInterface
     */
     public function createSessionDescription()
     {
         $class = $this->getClass();

         return new $class();
     }
}
