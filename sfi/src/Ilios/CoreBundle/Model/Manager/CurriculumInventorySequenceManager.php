<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceManagerInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

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
