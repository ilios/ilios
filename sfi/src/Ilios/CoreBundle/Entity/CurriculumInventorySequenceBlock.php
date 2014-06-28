<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CurriculumInventorySequenceBlock
 */
class CurriculumInventorySequenceBlock
{
    /**
     * @var integer
     */
    private $sequenceBlockId;

    /**
     * @var boolean
     */
    private $required;

    /**
     * @var boolean
     */
    private $childSequenceOrder;

    /**
     * @var integer
     */
    private $orderInSequence;

    /**
     * @var integer
     */
    private $minimum;

    /**
     * @var integer
     */
    private $maximum;

    /**
     * @var boolean
     */
    private $track;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var \Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel
     */
    private $academicLevel;

    /**
     * @var \Ilios\CoreBundle\Entity\Course
     */
    private $course;

    /**
     * @var \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock
     */
    private $parentSequenceBlock;

    /**
     * @var \Ilios\CoreBundle\Entity\CurriculumInventoryReport
     */
    private $report;


    /**
     * Get sequenceBlockId
     *
     * @return integer 
     */
    public function getSequenceBlockId()
    {
        return $this->sequenceBlockId;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return CurriculumInventorySequenceBlock
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean 
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set childSequenceOrder
     *
     * @param boolean $childSequenceOrder
     * @return CurriculumInventorySequenceBlock
     */
    public function setChildSequenceOrder($childSequenceOrder)
    {
        $this->childSequenceOrder = $childSequenceOrder;

        return $this;
    }

    /**
     * Get childSequenceOrder
     *
     * @return boolean 
     */
    public function getChildSequenceOrder()
    {
        return $this->childSequenceOrder;
    }

    /**
     * Set orderInSequence
     *
     * @param integer $orderInSequence
     * @return CurriculumInventorySequenceBlock
     */
    public function setOrderInSequence($orderInSequence)
    {
        $this->orderInSequence = $orderInSequence;

        return $this;
    }

    /**
     * Get orderInSequence
     *
     * @return integer 
     */
    public function getOrderInSequence()
    {
        return $this->orderInSequence;
    }

    /**
     * Set minimum
     *
     * @param integer $minimum
     * @return CurriculumInventorySequenceBlock
     */
    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;

        return $this;
    }

    /**
     * Get minimum
     *
     * @return integer 
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * Set maximum
     *
     * @param integer $maximum
     * @return CurriculumInventorySequenceBlock
     */
    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;

        return $this;
    }

    /**
     * Get maximum
     *
     * @return integer 
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * Set track
     *
     * @param boolean $track
     * @return CurriculumInventorySequenceBlock
     */
    public function setTrack($track)
    {
        $this->track = $track;

        return $this;
    }

    /**
     * Get track
     *
     * @return boolean 
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CurriculumInventorySequenceBlock
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
     * Set title
     *
     * @param string $title
     * @return CurriculumInventorySequenceBlock
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return CurriculumInventorySequenceBlock
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
     * @return CurriculumInventorySequenceBlock
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
     * Set duration
     *
     * @param integer $duration
     * @return CurriculumInventorySequenceBlock
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set academicLevel
     *
     * @param \Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel $academicLevel
     * @return CurriculumInventorySequenceBlock
     */
    public function setAcademicLevel(\Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel $academicLevel = null)
    {
        $this->academicLevel = $academicLevel;

        return $this;
    }

    /**
     * Get academicLevel
     *
     * @return \Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel 
     */
    public function getAcademicLevel()
    {
        return $this->academicLevel;
    }

    /**
     * Set course
     *
     * @param \Ilios\CoreBundle\Entity\Course $course
     * @return CurriculumInventorySequenceBlock
     */
    public function setCourse(\Ilios\CoreBundle\Entity\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Ilios\CoreBundle\Entity\Course 
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set parentSequenceBlock
     *
     * @param \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock $parentSequenceBlock
     * @return CurriculumInventorySequenceBlock
     */
    public function setParentSequenceBlock(
        CurriculumInventorySequenceBlock $parentSequenceBlock = null
    ) {
        $this->parentSequenceBlock = $parentSequenceBlock;

        return $this;
    }

    /**
     * Get parentSequenceBlock
     *
     * @return \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock 
     */
    public function getParentSequenceBlock()
    {
        return $this->parentSequenceBlock;
    }

    /**
     * Set report
     *
     * @param \Ilios\CoreBundle\Entity\CurriculumInventoryReport $report
     * @return CurriculumInventorySequenceBlock
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
