<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\PublishEventManagerInterface;
use Ilios\CoreBundle\Model\PublishEventInterface;

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
