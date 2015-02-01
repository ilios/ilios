<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @param int $orderInSequence
     */
    public function setOrderInSequence($orderInSequence);

    /**
     * @return int
     */
    public function getOrderInSequence();

    /**
     * @param int $minimum
     */
    public function setMinimum($minimum);

    /**
     * @return int
     */
    public function getMinimum();

    /**
     * @param int $maximum
     */
    public function setMaximum($maximum);

    /**
     * @return int
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
     * @param int $duration
     */
    public function setDuration($duration);

    /**
     * @return int
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
     * @param Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * @param CurriculumInventorySequenceBlockInterface $child
     */
    public function addChild(CurriculumInventorySequenceBlockInterface $child);

    /**
     * @return ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     */
    public function getChildren();

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report);

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport();
}

