<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Traits\DescribableEntityInterface;

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
