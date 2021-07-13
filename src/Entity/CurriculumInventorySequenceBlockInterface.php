<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SessionsEntityInterface;
use App\Traits\TitledEntityInterface;

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
    public const REQUIRED = 1;
    /**
     * @var int
     */
    public const OPTIONAL = 2;
    /**
     * @var int
     */
    public const REQUIRED_IN_TRACK = 3;

    /**
     * @var int
     */
    public const ORDERED = 1;

    /**
     * @var int
     */
    public const UNORDERED = 2;

    /**
     * @var int
     */
    public const PARALLEL = 3;

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
     * @param bool $track
     */
    public function setTrack($track);

    /**
     * @return bool
     */
    public function hasTrack();

    public function setStartDate(\DateTime $startDate = null);

    /**
     * @return \DateTime
     */
    public function getStartDate();

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

    public function setChildren(Collection $children);

    public function addChild(CurriculumInventorySequenceBlockInterface $child);

    public function removeChild(CurriculumInventorySequenceBlockInterface $child);

    /**
     * @return ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     */
    public function getChildren();

    public function setReport(CurriculumInventoryReportInterface $report);

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport();

    public function setParent(CurriculumInventorySequenceBlockInterface $parent = null);

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function getParent();

    /**
     * Sorts child sequence blocks according to this entity's child sequence order.
     * @return CurriculumInventorySequenceBlockInterface[] The sorted list of child sequence blocks.
     */
    public function getChildrenAsSortedList();

    public function setExcludedSessions(Collection $sessions);

    public function addExcludedSession(SessionInterface $session);

    public function removeExcludedSession(SessionInterface $session);

    /**
     * @return SessionInterface[]|ArrayCollection
     */
    public function getExcludedSessions();
}
