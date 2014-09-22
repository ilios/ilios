<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CourseClerkshipTypeManagerInterface;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * CourseClerkshipTypeManager
 */
abstract class CourseClerkshipTypeManager implements CourseClerkshipTypeManagerInterface
{
    /**
     * @return CourseClerkshipTypeInterface
     */
     public function createCourseClerkshipType()
     {
         $class = $this->getClass();

         return new $class();
     }
}
