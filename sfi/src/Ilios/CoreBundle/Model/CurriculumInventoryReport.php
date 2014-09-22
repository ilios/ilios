<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * CurriculumInventoryReport
 */
class CurriculumInventoryReport
{
    /**
     * @var integer
     */
    private $reportId;

    /**
     * @var integer
     */
    private $year;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var \Ilios\CoreBundle\Model\CurriculumInventoryExport
     */
    private $export;

    /**
     * @var \Ilios\CoreBundle\Model\CurriculumInventorySequence
     */
    private $sequence;

    /**
     * @var \Ilios\CoreBundle\Model\Program
     */
    private $program;


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
     * Set year
     *
     * @param integer $year
     * @return CurriculumInventoryReport
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CurriculumInventoryReport
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CurriculumInventoryReport
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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return CurriculumInventoryReport
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return CurriculumInventoryReport
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set export
     *
     * @param \Ilios\CoreBundle\Model\CurriculumInventoryExport $export
     * @return CurriculumInventoryReport
     */
    public function setExport(\Ilios\CoreBundle\Model\CurriculumInventoryExport $export = null)
    {
        $this->export = $export;

        return $this;
    }

    /**
     * Get export
     *
     * @return \Ilios\CoreBundle\Model\CurriculumInventoryExport 
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * Set sequence
     *
     * @param \Ilios\CoreBundle\Model\CurriculumInventorySequence $sequence
     * @return CurriculumInventoryReport
     */
    public function setSequence(\Ilios\CoreBundle\Model\CurriculumInventorySequence $sequence = null)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return \Ilios\CoreBundle\Model\CurriculumInventorySequence 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set program
     *
     * @param \Ilios\CoreBundle\Model\Program $program
     * @return CurriculumInventoryReport
     */
    public function setProgram(\Ilios\CoreBundle\Model\Program $program = null)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program
     *
     * @return \Ilios\CoreBundle\Model\Program 
     */
    public function getProgram()
    {
        return $this->program;
    }
}
