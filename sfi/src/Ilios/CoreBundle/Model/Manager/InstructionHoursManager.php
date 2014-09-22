<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\InstructionHoursManagerInterface;
use Ilios\CoreBundle\Entity\InstructionHoursInterface;

/**
 * InstructionHoursManager
 */
abstract class InstructionHoursManager implements InstructionHoursManagerInterface
{
    /**
     * @return InstructionHoursInterface
     */
     public function createInstructionHours()
     {
         $class = $this->getClass();

         return new $class();
     }
}
