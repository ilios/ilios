<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\SessionTypeManagerInterface;
use Ilios\CoreBundle\Model\SessionTypeInterface;

/**
 * SessionTypeManager
 */
abstract class SessionTypeManager implements SessionTypeManagerInterface
{
    /**
    * @return SessionTypeInterface
    */
    public function createSessionType()
    {
        $class = $this->getClass();

        return new $class();
    }
}
