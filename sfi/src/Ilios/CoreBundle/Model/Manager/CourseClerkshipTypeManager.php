<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CourseClerkshipTypeManagerInterface;
use Ilios\CoreBundle\Model\CourseClerkshipTypeInterface;

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
