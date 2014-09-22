<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CISessionManagerInterface;
use Ilios\CoreBundle\Entity\CISessionInterface;

/**
 * CISessionManager
 */
abstract class CISessionManager implements CISessionManagerInterface
{
    /**
     * @return CISessionInterface
     */
     public function createCISession()
     {
         $class = $this->getClass();

         return new $class();
     }
}
