<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\AlertManagerInterface;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * AlertManager
 */
abstract class AlertManager implements AlertManagerInterface
{
    /**
     * @return AlertInterface
     */
     public function createAlert()
     {
         $class = $this->getClass();

         return new $class();
     }
}
