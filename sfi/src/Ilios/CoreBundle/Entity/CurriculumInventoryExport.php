<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var \Ilios\CoreBundle\Entity\CurriculumInventoryReport
     */
    private $report;

    /**
     * @var \Ilios\CoreBundle\Entity\User
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
     * @param \Ilios\CoreBundle\Entity\CurriculumInventoryReport $report
     * @return CurriculumInventoryExport
     */
    public function setReport(\Ilios\CoreBundle\Entity\CurriculumInventoryReport $report = null)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return \Ilios\CoreBundle\Entity\CurriculumInventoryReport 
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Set createdBy
     *
     * @param \Ilios\CoreBundle\Entity\User $createdBy
     * @return CurriculumInventoryExport
     */
    public function setCreatedBy(\Ilios\CoreBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Ilios\CoreBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
