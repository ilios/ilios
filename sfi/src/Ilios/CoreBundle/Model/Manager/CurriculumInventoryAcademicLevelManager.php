<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryAcademicLevelManagerInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * CurriculumInventoryAcademicLevelManager
 */
abstract class CurriculumInventoryAcademicLevelManager implements CurriculumInventoryAcademicLevelManagerInterface
{
    /**
     * @return CurriculumInventoryAcademicLevelInterface
     */
     public function createCurriculumInventoryAcademicLevel()
     {
         $class = $this->getClass();

         return new $class();
     }
}
