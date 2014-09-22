<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\ObjectiveManagerInterface;
use Ilios\CoreBundle\Model\ObjectiveInterface;

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
