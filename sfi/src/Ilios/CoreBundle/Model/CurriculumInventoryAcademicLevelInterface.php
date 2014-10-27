<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableTraitInterface;
use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;
use Ilios\CoreBundle\Traits\NameableTraitInterface;

use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;

/**
 * Interface CurriculumInventoryAcademicLevelInterface
 */
interface CurriculumInventoryAcademicLevelInterface extends
    IdentifiableTraitInterface,
    NameableTraitInterface,
    DescribableTraitInterface
{
    /**
     * @param integer $level
     */
    public function setLevel($level);

    /**
     * @return integer
     */
    public function getLevel();

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report);

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport();
}

