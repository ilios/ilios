<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\BlameableTraitInterface;
use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;
use Ilios\CoreBundle\Traits\TimestampableTraitinterface;

use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;

/**
 * Interface CurriculumInventoryExportInterface
 */
interface CurriculumInventoryExportInterface extends
    BlameableTraitInterface,
    TimestampableTraitinterface,
    IdentifiableTraitIntertface
{
    /**
     * @param string $document
     */
    public function setDocument($document);

    /**
     * @return string
     */
    public function getDocument();

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report);

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport();
}
