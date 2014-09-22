<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\InstructorGroupManagerInterface;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

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
