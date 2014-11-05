<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReportPoValue
 */
class ReportPoValue
{
    /**
     * @var int
     */
    protected $reportId;

    /**
     * @var string
     */
    protected $prepositionalObjectTableRowId;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * @var \Ilios\CoreBundle\Model\Report
     */
    protected $report;


    /**
     * Set reportId
     *
     * @param int $reportId
     * @return ReportPoValue
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;

        return $this;
    }

    /**
     * Get reportId
     *
     * @return int 
     */
    public function getReportId()
    {
        return $this->reportId;
    }

    /**
     * Set prepositionalObjectTableRowId
     *
     * @param string $prepositionalObjectTableRowId
     * @return ReportPoValue
     */
    public function setPrepositionalObjectTableRowId($prepositionalObjectTableRowId)
    {
        $this->prepositionalObjectTableRowId = $prepositionalObjectTableRowId;

        return $this;
    }

    /**
     * Get prepositionalObjectTableRowId
     *
     * @return string 
     */
    public function getPrepositionalObjectTableRowId()
    {
        return $this->prepositionalObjectTableRowId;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return ReportPoValue
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set report
     *
     * @param \Ilios\CoreBundle\Model\Report $report
     * @return ReportPoValue
     */
    public function setReport(\Ilios\CoreBundle\Model\Report $report = null)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return \Ilios\CoreBundle\Model\Report 
     */
    public function getReport()
    {
        return $this->report;
    }
}
