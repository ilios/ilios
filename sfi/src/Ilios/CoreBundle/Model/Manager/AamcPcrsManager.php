<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\AamcPcrsManagerInterface;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * AamcPcrsManager
 */
abstract class AamcPcrsManager implements AamcPcrsManagerInterface
{
    /**
     * @return AamcPcrsInterface
     */
     public function createAamcPcrs()
     {
         $class = $this->getClass();

         return new $class();
     }
}
