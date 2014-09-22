<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\OfferingManagerInterface;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * OfferingManager
 */
abstract class OfferingManager implements OfferingManagerInterface
{
    /**
     * @return OfferingInterface
     */
     public function createOffering()
     {
         $class = $this->getClass();

         return new $class();
     }
}
