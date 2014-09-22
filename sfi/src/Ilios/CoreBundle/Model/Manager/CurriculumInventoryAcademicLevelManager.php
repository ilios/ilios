<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CurriculumInventoryAcademicLevelManagerInterface;
use Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevelInterface;

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
