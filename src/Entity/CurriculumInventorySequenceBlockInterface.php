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

interface CurriculumInventorySequenceBlockInterface extends
    IdentifiableEntityInterface,
    DescribableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    SessionsEntityInterface
{
    public const REQUIRED = 1;
    public const OPTIONAL = 2;
    public const REQUIRED_IN_TRACK = 3;

    public const ORDERED = 1;
    public const UNORDERED = 2;
    public const PARALLEL = 3;

    public function setRequired(int $required);
    public function getRequired(): int;

    public function setChildSequenceOrder(int $childSequenceOrder);
    public function getChildSequenceOrder(): int;

    public function setOrderInSequence(int $orderInSequence);
    public function getOrderInSequence(): int;

    public function setMinimum(int $minimum);
    public function getMinimum(): int;

    public function setMaximum(int $maximum);
    public function getMaximum(): int;

    public function setTrack(bool $track);
    public function hasTrack(): bool;

    public function setStartDate(DateTime $startDate = null);
    public function getStartDate(): ?DateTime;

    public function setEndDate(DateTime $endDate = null);
    public function getEndDate(): ?DateTime;

    public function setDuration(int $duration);
    public function getDuration(): int;

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

    public function setStartingAcademicLevel(CurriculumInventoryAcademicLevelInterface $level): void;
    public function setEndingAcademicLevel(CurriculumInventoryAcademicLevelInterface $level): void;
    public function getStartingAcademicLevel(): CurriculumInventoryAcademicLevelInterface;
    public function getEndingAcademicLevel(): CurriculumInventoryAcademicLevelInterface;
}
