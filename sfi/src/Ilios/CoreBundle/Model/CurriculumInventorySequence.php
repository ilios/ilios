<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class CurriculumInventorySequence
 * @package Ilios\CoreBundle\Model
 */
class CurriculumInventorySequence implements CurriculumInventorySequenceInterface
{
//    use IdentifiableEntity;
    use DescribableEntity;

    /**
     * @var CurriculumInventoryReportInterface
     */
    protected $report;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        throw new \LogicException('This is not implemented yet.');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->report->getId();
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
