<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\SessionManagerInterface;
use Ilios\CoreBundle\Model\SessionInterface;

/**
 * SessionManager
 */
abstract class SessionManager implements SessionManagerInterface
{
    /**
    * @return SessionInterface
    */
    public function createSession()
    {
        $class = $this->getClass();

        return new $class();
    }
}
