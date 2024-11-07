<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableNullableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SessionsEntityInterface;
use App\Traits\TitledEntityInterface;

interface CurriculumInventorySequenceBlockInterface extends
    IdentifiableEntityInterface,
    DescribableNullableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    SessionsEntityInterface
{
    public const int REQUIRED = 1;
    public const int OPTIONAL = 2;
    public const int REQUIRED_IN_TRACK = 3;

    public const int ORDERED = 1;
    public const int UNORDERED = 2;
    public const int PARALLEL = 3;

    public function setRequired(int $required): void;
    public function getRequired(): int;

    public function setChildSequenceOrder(int $childSequenceOrder): void;
    public function getChildSequenceOrder(): int;

    public function setOrderInSequence(int $orderInSequence): void;
    public function getOrderInSequence(): int;

    public function setMinimum(int $minimum): void;
    public function getMinimum(): int;

    public function setMaximum(int $maximum): void;
    public function getMaximum(): int;

    public function setTrack(bool $track): void;
    public function hasTrack(): bool;

    public function setStartDate(?DateTime $startDate = null): void;
    public function getStartDate(): ?DateTime;

    public function setEndDate(?DateTime $endDate = null): void;
    public function getEndDate(): ?DateTime;

    public function setDuration(int $duration): void;
    public function getDuration(): int;

    public function setCourse(?CourseInterface $course = null): void;
    public function getCourse(): ?CourseInterface;

    public function setChildren(Collection $children): void;
    public function addChild(CurriculumInventorySequenceBlockInterface $child): void;
    public function removeChild(CurriculumInventorySequenceBlockInterface $child): void;
    public function getChildren(): Collection;

    public function setReport(CurriculumInventoryReportInterface $report): void;
    public function getReport(): CurriculumInventoryReportInterface;

    public function setParent(?CurriculumInventorySequenceBlockInterface $parent = null): void;
    public function getParent(): ?CurriculumInventorySequenceBlockInterface;

    /**
     * Sorts child sequence blocks according to this entity's child sequence order.
     */
    public function getChildrenAsSortedList(): array;

    public function setExcludedSessions(Collection $sessions): void;
    public function addExcludedSession(SessionInterface $session): void;
    public function removeExcludedSession(SessionInterface $session): void;
    public function getExcludedSessions(): Collection;

    public function setStartingAcademicLevel(?CurriculumInventoryAcademicLevelInterface $level = null): void;
    public function setEndingAcademicLevel(CurriculumInventoryAcademicLevelInterface $level): void;
    public function getStartingAcademicLevel(): CurriculumInventoryAcademicLevelInterface;
    public function getEndingAcademicLevel(): CurriculumInventoryAcademicLevelInterface;
}
