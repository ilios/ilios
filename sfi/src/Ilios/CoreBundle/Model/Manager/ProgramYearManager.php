<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\ProgramYearManagerInterface;
use Ilios\CoreBundle\Model\ProgramYearInterface;

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
