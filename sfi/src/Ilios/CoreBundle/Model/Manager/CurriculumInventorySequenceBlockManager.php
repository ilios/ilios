<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CurriculumInventorySequenceBlockManagerInterface;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockInterface;

/**
 * CurriculumInventorySequenceBlockManager
 */
abstract class CurriculumInventorySequenceBlockManager implements CurriculumInventorySequenceBlockManagerInterface
{
    /**
    * @return CurriculumInventorySequenceBlockInterface
    */
    public function createCurriculumInventorySequenceBlock()
    {
        $class = $this->getClass();

        return new $class();
    }
}
