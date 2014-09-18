<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReportPoValue
 */
class ReportPoValue
{
    /**
     * @var integer
     */
    private $reportId;

    /**
     * @var string
     */
    private $prepositionalObjectTableRowId;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var \Ilios\CoreBundle\Entity\Report
     */
    private $report;


    /**
     * Set reportId
     *
     * @param integer $reportId
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
     * @return integer 
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
     * @param \Ilios\CoreBundle\Entity\Report $report
     * @return ReportPoValue
     */
    public function setReport(\Ilios\CoreBundle\Entity\Report $report = null)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return \Ilios\CoreBundle\Entity\Report 
     */
    public function getReport()
    {
        return $this->report;
    }
}
