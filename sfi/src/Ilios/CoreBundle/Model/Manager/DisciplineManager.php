<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\DisciplineManagerInterface;
use Ilios\CoreBundle\Entity\DisciplineInterface;

/**
 * DisciplineManager
 */
abstract class DisciplineManager implements DisciplineManagerInterface
{
    /**
     * @return DisciplineInterface
     */
     public function createDiscipline()
     {
         $class = $this->getClass();

         return new $class();
     }
}
