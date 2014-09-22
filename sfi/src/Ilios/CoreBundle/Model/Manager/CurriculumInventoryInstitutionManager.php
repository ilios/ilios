<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CurriculumInventoryInstitutionManagerInterface;
use Ilios\CoreBundle\Model\CurriculumInventoryInstitutionInterface;

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
