<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;

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
