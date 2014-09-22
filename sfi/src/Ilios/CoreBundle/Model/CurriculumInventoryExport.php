<?php

namespace Ilios\CoreBundle\Model;



/**
 * CurriculumInventoryExport
 */
class CurriculumInventoryExport
{
    /**
     * @var integer
     */
    private $reportId;

    /**
     * @var string
     */
    private $document;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Ilios\CoreBundle\Model\CurriculumInventoryReport
     */
    private $report;

    /**
     * @var \Ilios\CoreBundle\Model\User
     */
    private $createdBy;


    /**
     * Set reportId
     *
     * @param integer $reportId
     * @return CurriculumInventoryExport
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
     * Set document
     *
     * @param string $document
     * @return CurriculumInventoryExport
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return string 
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return CurriculumInventoryExport
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime 
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set report
     *
     * @param \Ilios\CoreBundle\Model\CurriculumInventoryReport $report
     * @return CurriculumInventoryExport
     */
    public function setReport(\Ilios\CoreBundle\Model\CurriculumInventoryReport $report = null)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return \Ilios\CoreBundle\Model\CurriculumInventoryReport 
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Set createdBy
     *
     * @param \Ilios\CoreBundle\Model\User $createdBy
     * @return CurriculumInventoryExport
     */
    public function setCreatedBy(\Ilios\CoreBundle\Model\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Ilios\CoreBundle\Model\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
