<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\ProgramYearStewardManagerInterface;
use Ilios\CoreBundle\Model\ProgramYearStewardInterface;

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
