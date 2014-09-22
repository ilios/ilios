<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceBlockManagerInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

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
