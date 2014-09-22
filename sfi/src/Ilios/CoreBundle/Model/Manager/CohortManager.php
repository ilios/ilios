<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CohortManagerInterface;
use Ilios\CoreBundle\Entity\CohortInterface;

/**
 * CohortManager
 */
abstract class CohortManager implements CohortManagerInterface
{
    /**
     * @return CohortInterface
     */
     public function createCohort()
     {
         $class = $this->getClass();

         return new $class();
     }
}
