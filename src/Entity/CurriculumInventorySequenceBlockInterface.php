<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
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

    public function getRequired(): int;

    /**
     * @param int $childSequenceOrder
     */
    public function setChildSequenceOrder($childSequenceOrder);

    public function getChildSequenceOrder(): ?int;

    /**
     * @param int $orderInSequence
     */
    public function setOrderInSequence($orderInSequence);

    public function getOrderInSequence(): ?int;

    /**
     * @param int $minimum
     */
    public function setMinimum($minimum);

    public function getMinimum(): ?int;

    /**
     * @param int $maximum
     */
    public function setMaximum($maximum);

    public function getMaximum(): ?int;

    /**
     * @param bool $track
     */
    public function setTrack($track);

    public function hasTrack(): bool;

    public function setStartDate(DateTime $startDate = null);

    public function getStartDate(): ?DateTime;

    public function setEndDate(DateTime $endDate = null);

    public function getEndDate(): ?DateTime;

    /**
     * @param int $duration
     */
    public function setDuration($duration);

    public function getDuration(): ?int;

    public function setAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel);

    public function getAcademicLevel(): CurriculumInventoryAcademicLevelInterface;

    public function setCourse(CourseInterface $course = null);

    public function getCourse(): ?CourseInterface;

    public function setChildren(Collection $children);

    public function addChild(CurriculumInventorySequenceBlockInterface $child);

    public function removeChild(CurriculumInventorySequenceBlockInterface $child);

    public function getChildren(): Collection;

    public function setReport(CurriculumInventoryReportInterface $report);

    public function getReport(): CurriculumInventoryReportInterface;

    public function setParent(CurriculumInventorySequenceBlockInterface $parent = null);

    public function getParent(): ?CurriculumInventorySequenceBlockInterface;

    /**
     * Sorts child sequence blocks according to this entity's child sequence order.
     */
    public function getChildrenAsSortedList(): array;

    public function setExcludedSessions(Collection $sessions);

    public function addExcludedSession(SessionInterface $session);

    public function removeExcludedSession(SessionInterface $session);

    public function getExcludedSessions(): Collection;
}
