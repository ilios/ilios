<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CISessionManagerInterface;
use Ilios\CoreBundle\Model\CISessionInterface;

/**
 * CISessionManager
 */
abstract class CISessionManager implements CISessionManagerInterface
{
    /**
    * @return CISessionInterface
    */
    public function createCISession()
    {
        $class = $this->getClass();

        return new $class();
    }
}
