<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface CurriculumInventorySequenceBlockInterface
 */
interface CurriculumInventorySequenceBlockInterface extends
    IdentifiableEntityInterface,
    DescribableEntityInterface,
    TitledEntityInterface
{
    /**
     * @param boolean $required
     */
    public function setRequired($required);

    /**
     * @return boolean
     */
    public function isRequired();

    /**
     * @param boolean $childSequenceOrder
     */
    public function setChildSequenceOrder($childSequenceOrder);

    /**
     * @return boolean
     */
    public function hasChildSequenceOrder();

    /**
     * @param integer $orderInSequence
     */
    public function setOrderInSequence($orderInSequence);

    /**
     * @return integer
     */
    public function getOrderInSequence();

    /**
     * @param integer $minimum
     */
    public function setMinimum($minimum);

    /**
     * @return integer
     */
    public function getMinimum();

    /**
     * @param integer $maximum
     */
    public function setMaximum($maximum);

    /**
     * @return integer
     */
    public function getMaximum();

    /**
     * @param boolean $track
     */
    public function setTrack($track);

    /**
     * @return boolean
     */
    public function hasTrack();

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate);

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate);

    /**
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * @param integer $duration
     */
    public function setDuration($duration);

    /**
     * @return integer
     */
    public function getDuration();

    /**
     * @param CurriculumInventoryAcademicLevelInterface $academicLevel
     */
    public function setAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel);

    /**
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function getAcademicLevel();

    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course);

    /**
     * @return CourseInterface
     */
    public function getCourse();

    /**
     * @param CurriculumInventorySequenceBlockInterface $parentSequenceBlock
     */
    public function setParentSequenceBlock(CurriculumInventorySequenceBlockInterface $parentSequenceBlock);

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function getParentSequenceBlock();

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report);

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport();
}

