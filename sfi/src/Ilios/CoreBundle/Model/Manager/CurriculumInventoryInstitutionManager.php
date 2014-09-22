<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManagerInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;

/**
 * CurriculumInventoryInstitutionManager
 */
abstract class CurriculumInventoryInstitutionManager implements CurriculumInventoryInstitutionManagerInterface
{
    /**
     * @return CurriculumInventoryInstitutionInterface
     */
     public function createCurriculumInventoryInstitution()
     {
         $class = $this->getClass();

         return new $class();
     }
}
