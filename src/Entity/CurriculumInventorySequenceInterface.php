<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Traits\DescribableEntityInterface;

/**
 * Interface CurriculumInventorySequenceInterface
 */
interface CurriculumInventorySequenceInterface extends
    DescribableEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report);

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport();
}
