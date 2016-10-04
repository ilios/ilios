<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SessionsEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface CurriculumInventorySequenceBlockInterface
 */
interface CurriculumInventorySequenceBlockInterface extends
    IdentifiableEntityInterface,
    DescribableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    SessionsEntityInterface
{
    /**
     * @var int
     */
    const REQUIRED = 1;
    /**
     * @var int
     */
    const OPTIONAL = 2;
    /**
     * @var int
     */
    const REQUIRED_IN_TRACK = 3;

    /**
     * @var int
     */
    const ORDERED = 1;

    /**
     * @var int
     */
    const UNORDERED = 2;

    /**
     * @var int
     */
    const PARALLEL = 3;

    /**
     * @param int $required
     */
    public function setRequired($required);

    /**
     * @return int
     */
    public function getRequired();

    /**
     * @param int $childSequenceOrder
     */
    public function setChildSequenceOrder($childSequenceOrder);

    /**
     * @return int
     */
    public function getChildSequenceOrder();

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
    public function setStartDate(\DateTime $startDate = null);

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate = null);

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
     * @param CourseInterface $course|null
     */
    public function setCourse(CourseInterface $course = null);

    /**
     * @return CourseInterface|null
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
     * @param CurriculumInventorySequenceBlockInterface $child
     */
    public function removeChild(CurriculumInventorySequenceBlockInterface $child);

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

    /**
     * @param CurriculumInventorySequenceBlockInterface $parent
     */
    public function setParent(CurriculumInventorySequenceBlockInterface $parent);

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function getParent();

    /**
     * Sorts child sequence blocks according to this entity's child sequence order.
     * @return CurriculumInventorySequenceBlockInterface[] The sorted list of child sequence blocks.
     */
    public function getChildrenAsSortedList();
}
