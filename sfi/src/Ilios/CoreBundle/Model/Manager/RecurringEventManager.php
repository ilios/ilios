<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\RecurringEventManagerInterface;
use Ilios\CoreBundle\Model\RecurringEventInterface;

/**
 * RecurringEventManager
 */
abstract class RecurringEventManager implements RecurringEventManagerInterface
{
    /**
    * @return RecurringEventInterface
    */
    public function createRecurringEvent()
    {
        $class = $this->getClass();

        return new $class();
    }
}
