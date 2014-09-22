<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\ProgramManagerInterface;
use Ilios\CoreBundle\Model\ProgramInterface;

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
