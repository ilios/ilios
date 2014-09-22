<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface CurriculumInventorySequenceInterface
 */
interface CurriculumInventorySequenceInterface 
{
    public function setReportId($reportId);

    public function getReportId();

    public function setDescription($description);

    public function getDescription();

    public function setReport(\Ilios\CoreBundle\Model\CurriculumInventoryReport $report = null);

    public function getReport();
}

