<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * CurriculumInventorySequenceBlock
 */
class CurriculumInventorySequenceBlock implements CurriculumInventorySequenceBlockInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use TitledEntity;

    /**
     * @var boolean
     */
    protected $required;

    /**
     * @var boolean
     */
    protected $childSequenceOrder;

    /**
     * @var integer
     */
    protected $orderInSequence;

    /**
     * @var integer
     */
    protected $minimum;

    /**
     * @var integer
     */
    protected $maximum;

    /**
     * @var boolean
     */
    protected $track;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var integer
     */
    protected $duration;

    /**
     * @var CurriculumInventoryAcademicLevelInterface
     */
    protected $academicLevel;

    /**
     * @var CourseInterface
     */
    protected $course;

    /**
     * @var CurriculumInventorySequenceBlockInterface
     */
    protected $parentSequenceBlock;

    /**
     * @var CurriculumInventoryReportInterface
     */
    protected $report;

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $childSequenceOrder
     */
    public function setChildSequenceOrder($childSequenceOrder)
    {
        $this->childSequenceOrder = $childSequenceOrder;
    }

    /**
     * @return boolean
     */
    public function hasChildSequenceOrder()
    {
        return $this->childSequenceOrder;
    }

    /**
     * @param integer $orderInSequence
     */
    public function setOrderInSequence($orderInSequence)
    {
        $this->orderInSequence = $orderInSequence;
    }

    /**
     * @return integer
     */
    public function getOrderInSequence()
    {
        return $this->orderInSequence;
    }

    /**
     * @param integer $minimum
     */
    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * @return integer
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * @param integer $maximum
     */
    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;
    }

    /**
     * @return integer
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * @param boolean $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
    }

    /**
     * @return boolean
     */
    public function hasTrack()
    {
        return $this->track;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param integer $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $academicLevel
     */
    public function setAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel)
    {
        $this->academicLevel = $academicLevel;
    }

    /**
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function getAcademicLevel()
    {
        return $this->academicLevel;
    }

    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course)
    {
        $this->course = $course;
    }

    /**
     * @return CourseInterface
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $parentSequenceBlock
     */
    public function setParentSequenceBlock(CurriculumInventorySequenceBlockInterface $parentSequenceBlock)
    {
        $this->parentSequenceBlock = $parentSequenceBlock;
    }

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function getParentSequenceBlock()
    {
        return $this->parentSequenceBlock;
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report)
    {
        $this->report = $report;
    }

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }
}
