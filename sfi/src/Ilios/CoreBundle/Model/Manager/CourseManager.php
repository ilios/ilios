<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Model\CourseInterface;

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
