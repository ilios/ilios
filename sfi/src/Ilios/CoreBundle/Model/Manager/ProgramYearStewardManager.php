<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ProgramYearStewardManagerInterface;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

/**
 * ProgramYearStewardManager
 */
abstract class ProgramYearStewardManager implements ProgramYearStewardManagerInterface
{
    /**
     * @return ProgramYearStewardInterface
     */
     public function createProgramYearSteward()
     {
         $class = $this->getClass();

         return new $class();
     }
}
