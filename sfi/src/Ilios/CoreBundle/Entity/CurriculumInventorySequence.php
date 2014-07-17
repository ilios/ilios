<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CurriculumInventorySequence
 */
class CurriculumInventorySequence
{
    /**
     * @var integer
     */
    private $reportId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Ilios\CoreBundle\Entity\CurriculumInventoryReport
     */
    private $report;


    /**
     * Set reportId
     *
     * @param integer $reportId
     * @return CurriculumInventorySequence
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
     * Set description
     *
     * @param string $description
     * @return CurriculumInventorySequence
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set report
     *
     * @param \Ilios\CoreBundle\Entity\CurriculumInventoryReport $report
     * @return CurriculumInventorySequence
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
}
