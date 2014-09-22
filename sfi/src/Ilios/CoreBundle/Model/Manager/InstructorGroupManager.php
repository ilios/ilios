<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\InstructorGroupManagerInterface;
use Ilios\CoreBundle\Model\InstructorGroupInterface;

/**
 * InstructorGroupManager
 */
abstract class InstructorGroupManager implements InstructorGroupManagerInterface
{
    /**
    * @return InstructorGroupInterface
    */
    public function createInstructorGroup()
    {
        $class = $this->getClass();

        return new $class();
    }
}
