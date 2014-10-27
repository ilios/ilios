<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableTraitInterface;
use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;

/**
 * Interface CurriculumInventorySequenceInterface
 */
interface CurriculumInventorySequenceInterface extends
    IdentifiableTraitInterface,
    DescribableTraitInterface
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

