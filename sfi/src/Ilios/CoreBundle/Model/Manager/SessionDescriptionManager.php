<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\SessionDescriptionManagerInterface;
use Ilios\CoreBundle\Model\SessionDescriptionInterface;

/**
 * SessionDescriptionManager
 */
abstract class SessionDescriptionManager implements SessionDescriptionManagerInterface
{
    /**
    * @return SessionDescriptionInterface
    */
    public function createSessionDescription()
    {
        $class = $this->getClass();

        return new $class();
    }
}
