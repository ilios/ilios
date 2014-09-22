<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CompetencyManagerInterface;
use Ilios\CoreBundle\Entity\CompetencyInterface;

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
