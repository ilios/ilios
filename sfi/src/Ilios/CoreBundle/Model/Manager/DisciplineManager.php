<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\DisciplineManagerInterface;
use Ilios\CoreBundle\Model\DisciplineInterface;

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
