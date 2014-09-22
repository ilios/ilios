<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CompetencyManagerInterface;
use Ilios\CoreBundle\Model\CompetencyInterface;

/**
 * CompetencyManager
 */
abstract class CompetencyManager implements CompetencyManagerInterface
{
    /**
    * @return CompetencyInterface
    */
    public function createCompetency()
    {
        $class = $this->getClass();

        return new $class();
    }
}
