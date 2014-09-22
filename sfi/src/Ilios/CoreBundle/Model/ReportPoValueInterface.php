<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface ReportPoValueInterface
 */
interface ReportPoValueInterface 
{
    public function setReportId($reportId);

    public function getReportId();

    public function setPrepositionalObjectTableRowId($prepositionalObjectTableRowId);

    public function getPrepositionalObjectTableRowId();

    public function setDeleted($deleted);

    public function getDeleted();

    public function setReport(\Ilios\CoreBundle\Model\Report $report = null);

    public function getReport();
}

