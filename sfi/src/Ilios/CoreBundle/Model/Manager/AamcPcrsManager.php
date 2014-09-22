<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\AamcPcrsManagerInterface;
use Ilios\CoreBundle\Model\AamcPcrsInterface;

/**
 * AamcPcrsManager
 */
abstract class AamcPcrsManager implements AamcPcrsManagerInterface
{
    /**
    * @return AamcPcrsInterface
    */
    public function createAamcPcrs()
    {
        $class = $this->getClass();

        return new $class();
    }
}
