<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\AlertManagerInterface;
use Ilios\CoreBundle\Model\AlertInterface;

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
