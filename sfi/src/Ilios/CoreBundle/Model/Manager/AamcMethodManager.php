<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\AamcMethodManagerInterface;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

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
