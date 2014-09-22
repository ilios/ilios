<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\PublishEventManagerInterface;
use Ilios\CoreBundle\Entity\PublishEventInterface;

/**
 * PublishEventManager
 */
abstract class PublishEventManager implements PublishEventManagerInterface
{
    /**
     * @return PublishEventInterface
     */
     public function createPublishEvent()
     {
         $class = $this->getClass();

         return new $class();
     }
}
