<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;

use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryAcademicLevel
 * @package Ilios\CoreBundle\Model
 */
class CurriculumInventoryAcademicLevel implements CurriculumInventoryAcademicLevelInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use DescribableEntity;

    /**
     * @var integer
     */
    protected $level;

    /**
     * @var CurriculumInventoryReportInterface
     */
    protected $report;

    /**
     * @param integer $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

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
