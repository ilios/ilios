<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CurriculumInventoryExportManagerInterface;
use Ilios\CoreBundle\Model\CurriculumInventoryExportInterface;

/**
 * CurriculumInventoryExportManager
 */
abstract class CurriculumInventoryExportManager implements CurriculumInventoryExportManagerInterface
{
    /**
    * @return CurriculumInventoryExportInterface
    */
    public function createCurriculumInventoryExport()
    {
        $class = $this->getClass();

        return new $class();
    }
}
