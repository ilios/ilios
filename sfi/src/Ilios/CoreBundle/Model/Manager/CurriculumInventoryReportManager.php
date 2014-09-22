<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CurriculumInventoryReportManagerInterface;
use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;

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
