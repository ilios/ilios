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

interface IlmSessionInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface,
    LearnerGroupsEntityInterface,
    InstructorGroupsEntityInterface,
    InstructorsEntityInterface,
    LearnersEntityInterface
{
    public function setHours(float $hours);
    public function getHours(): float;

    public function setDueDate(DateTime $dueDate = null);
    public function getDueDate(): DateTime;

    /**
     * Get all the instructors including those in groups
     */
    public function getAllInstructors(): Collection;

    public function setSession(SessionInterface $session);
    public function getSession(): SessionInterface;

    /**
     * Get the school we belong to
     */
    public function getSchool(): ?SchoolInterface;
}
