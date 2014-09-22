<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\GroupManagerInterface;
use Ilios\CoreBundle\Model\GroupInterface;

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
