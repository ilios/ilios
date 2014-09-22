<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Model\SchoolInterface;

/**
 * SchoolManager
 */
abstract class SchoolManager implements SchoolManagerInterface
{
    /**
    * @return SchoolInterface
    */
    public function createSchool()
    {
        $class = $this->getClass();

        return new $class();
    }
}
