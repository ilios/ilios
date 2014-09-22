<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceBlockSessionManagerInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

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
