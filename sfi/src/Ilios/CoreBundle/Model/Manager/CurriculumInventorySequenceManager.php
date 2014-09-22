<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CurriculumInventorySequenceManagerInterface;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceInterface;

/**
 * CurriculumInventorySequenceManager
 */
abstract class CurriculumInventorySequenceManager implements CurriculumInventorySequenceManagerInterface
{
    /**
    * @return CurriculumInventorySequenceInterface
    */
    public function createCurriculumInventorySequence()
    {
        $class = $this->getClass();

        return new $class();
    }
}
