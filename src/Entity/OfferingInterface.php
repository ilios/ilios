<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\InstructorsEntityInterface;
use App\Traits\LearnerGroupsEntityInterface;
use App\Traits\LearnersEntityInterface;
use App\Traits\TimestampableEntityInterface;

interface OfferingInterface extends
    IdentifiableEntityInterface,
    TimestampableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface,
    LearnerGroupsEntityInterface,
    InstructorGroupsEntityInterface,
    InstructorsEntityInterface,
    LearnersEntityInterface
{
    public function setRoom(?string $room): void;
    public function getRoom(): ?string;

    public function setSite(?string $site): void;
    public function getSite(): ?string;

    public function setUrl(?string $url): void;
    public function getUrl(): ?string;

    public function setStartDate(DateTime $startDate): void;
    public function getStartDate(): DateTime;

    public function setEndDate(DateTime $endDate): void;
    public function getEndDate(): DateTime;

    public function setSession(SessionInterface $session): void;
    public function getSession(): SessionInterface;

    /**
     * Get all the instructors including those in groups
     */
    public function getAllInstructors(): Collection;

    /**
     * Returns "alertable" properties in an easy to compare format.
     */
    public function getAlertProperties(): array;

    public function getSchool(): SchoolInterface;
}
