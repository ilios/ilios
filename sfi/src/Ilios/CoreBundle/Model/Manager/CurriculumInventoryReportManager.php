<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManagerInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * CurriculumInventoryReportManager
 */
abstract class CurriculumInventoryReportManager implements CurriculumInventoryReportManagerInterface
{
    /**
     * @return CurriculumInventoryReportInterface
     */
     public function createCurriculumInventoryReport()
     {
         $class = $this->getClass();

         return new $class();
     }
}
