<?php

namespace Ilios\CoreBundle\Model;

use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ilios\CoreBundle\Traits\IdentifiableTrait;

use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;

/**
 * CurriculumInventoryExport
 */
class CurriculumInventoryExport implements CurriculumInventoryExportInterface
{
    use IdentifiableTrait;
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @var string
     */
    private $document;

    /**
     * @var CurriculumInventoryReportInterface
     */
    private $report;

    /**
     * @param string $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
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
