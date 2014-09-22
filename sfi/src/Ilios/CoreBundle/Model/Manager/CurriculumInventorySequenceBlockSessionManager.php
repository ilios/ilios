<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CurriculumInventorySequenceBlockSessionManagerInterface;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSessionInterface;

/**
 * CurriculumInventorySequenceBlockSessionManager
 */
abstract class CurriculumInventorySequenceBlockSessionManager implements CurriculumInventorySequenceBlockSessionManagerInterface
{
    /**
    * @return CurriculumInventorySequenceBlockSessionInterface
    */
    public function createCurriculumInventorySequenceBlockSession()
    {
        $class = $this->getClass();

        return new $class();
    }
}
