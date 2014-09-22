<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\GroupManagerInterface;
use Ilios\CoreBundle\Entity\GroupInterface;

/**
 * GroupManager
 */
abstract class GroupManager implements GroupManagerInterface
{
    /**
     * @return GroupInterface
     */
     public function createGroup()
     {
         $class = $this->getClass();

         return new $class();
     }
}
