<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\AamcMethodManagerInterface;
use Ilios\CoreBundle\Model\AamcMethodInterface;

/**
 * AamcMethodManager
 */
abstract class AamcMethodManager implements AamcMethodManagerInterface
{
    /**
    * @return AamcMethodInterface
    */
    public function createAamcMethod()
    {
        $class = $this->getClass();

        return new $class();
    }
}
