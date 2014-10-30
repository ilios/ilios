<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;


/**
 * CurriculumInventorySequence
 */
class CurriculumInventorySequence implements CurriculumInventorySequenceInterface
{
    use IdentifiableEntity;
    use DescribableEntity;

    /**
     * @var CurriculumInventoryReportInterface
     */
    protected $report;

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report)
    {
        $this->report = $report;
    }

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }
}
