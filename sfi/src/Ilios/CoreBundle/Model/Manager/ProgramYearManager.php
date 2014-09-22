<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ProgramYearManagerInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * ProgramYearManager
 */
abstract class ProgramYearManager implements ProgramYearManagerInterface
{
    /**
     * @return ProgramYearInterface
     */
     public function createProgramYear()
     {
         $class = $this->getClass();

         return new $class();
     }
}
