<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CohortManagerInterface;
use Ilios\CoreBundle\Model\CohortInterface;

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
