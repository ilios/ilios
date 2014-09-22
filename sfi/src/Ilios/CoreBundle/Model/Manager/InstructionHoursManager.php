<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\InstructionHoursManagerInterface;
use Ilios\CoreBundle\Model\InstructionHoursInterface;

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
