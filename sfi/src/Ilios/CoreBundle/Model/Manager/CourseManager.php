<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * CourseManager
 */
abstract class CourseManager implements CourseManagerInterface
{
    /**
     * @return CourseInterface
     */
     public function createCourse()
     {
         $class = $this->getClass();

         return new $class();
     }
}
