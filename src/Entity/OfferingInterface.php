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
use App\Traits\StringableEntityInterface;
use App\Traits\TimestampableEntityInterface;

/**
 * Interface OfferingInterface
 */
interface OfferingInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface,
    TimestampableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface,
    LearnerGroupsEntityInterface,
    InstructorGroupsEntityInterface,
    InstructorsEntityInterface,
    LearnersEntityInterface
{
    public function setRoom(?string $room);

    public function getRoom(): ?string;

    /**
     * @param string $site
     */
    public function setSite($site);

    public function getSite(): string;

    public function setUrl(?string $url);

    public function getUrl(): ?string;

    public function setStartDate(DateTime $startDate);

    public function getStartDate(): DateTime;

    public function setEndDate(DateTime $endDate);

    public function getEndDate(): DateTime;

    public function setSession(SessionInterface $session);

    public function getSession(): ?SessionInterface;

    /**
     * Get all the instructors including those in groups
     */
    public function getAllInstructors(): Collection;

    /**
     * Returns "alertable" properties in an easy to compare format.
     */
    public function getAlertProperties(): array;

    public function getSchool(): ?SchoolInterface;
}
