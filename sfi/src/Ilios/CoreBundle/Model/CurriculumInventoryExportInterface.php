<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface CurriculumInventoryExportInterface
 */
interface CurriculumInventoryExportInterface 
{
    public function setReportId($reportId);

    public function getReportId();

    public function setDocument($document);

    public function getDocument();

    public function setCreatedOn($createdOn);

    public function getCreatedOn();

    public function setReport(\Ilios\CoreBundle\Model\CurriculumInventoryReport $report = null);

    public function getReport();

    public function setCreatedBy(\Ilios\CoreBundle\Model\User $createdBy = null);

    public function getCreatedBy();
}
