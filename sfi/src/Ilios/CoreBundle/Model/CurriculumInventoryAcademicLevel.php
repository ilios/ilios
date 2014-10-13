<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableTrait;
use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\NameableTrait;

use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryAcademicLevel
 * @package Ilios\CoreBundle\Model
 */
class CurriculumInventoryAcademicLevel implements CurriculumInventoryAcademicLevelInterface
{
    use IdentifiableTrait;
    use NameableTrait;
    use DescribableTrait;

    /**
     * @var integer
     */
    private $level;

    /**
     * @var CurriculumInventoryReportInterface
     */
    private $report;

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
