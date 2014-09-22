<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ProgramManagerInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * ProgramManager
 */
abstract class ProgramManager implements ProgramManagerInterface
{
    /**
     * @return ProgramInterface
     */
     public function createProgram()
     {
         $class = $this->getClass();

         return new $class();
     }
}
