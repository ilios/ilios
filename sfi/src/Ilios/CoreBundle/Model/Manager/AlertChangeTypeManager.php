<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\AlertChangeTypeManagerInterface;
use Ilios\CoreBundle\Model\AlertChangeTypeInterface;

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
