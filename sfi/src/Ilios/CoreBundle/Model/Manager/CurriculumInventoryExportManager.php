<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryExportManagerInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;

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
