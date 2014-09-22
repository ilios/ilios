<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ObjectiveManagerInterface;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * ObjectiveManager
 */
abstract class ObjectiveManager implements ObjectiveManagerInterface
{
    /**
     * @return ObjectiveInterface
     */
     public function createObjective()
     {
         $class = $this->getClass();

         return new $class();
     }
}
