<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\RecurringEventManagerInterface;
use Ilios\CoreBundle\Entity\RecurringEventInterface;

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
