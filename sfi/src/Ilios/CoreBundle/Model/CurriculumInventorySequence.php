<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableTrait;
use Ilios\CoreBundle\Traits\IdentifiableTrait;


/**
 * CurriculumInventorySequence
 */
class CurriculumInventorySequence implements CurriculumInventorySequenceInterface
{
    use IdentifiableTrait;
    use DescribableTrait;

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
