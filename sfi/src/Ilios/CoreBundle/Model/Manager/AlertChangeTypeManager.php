<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\AlertChangeTypeManagerInterface;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * AlertChangeTypeManager
 */
abstract class AlertChangeTypeManager implements AlertChangeTypeManagerInterface
{
    /**
     * @return AlertChangeTypeInterface
     */
     public function createAlertChangeType()
     {
         $class = $this->getClass();

         return new $class();
     }
}
