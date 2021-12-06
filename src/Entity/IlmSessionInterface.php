<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\InstructorsEntityInterface;
use App\Traits\LearnerGroupsEntityInterface;
use App\Traits\LearnersEntityInterface;

/**
 * Interface IlmSessionInterface
 */
interface IlmSessionInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface,
    LearnerGroupsEntityInterface,
    InstructorGroupsEntityInterface,
    InstructorsEntityInterface,
    LearnersEntityInterface
{
    /**
     * @param float $hours
     */
    public function setHours($hours);

    public function getHours(): float;

    public function setDueDate(DateTime $dueDate = null);

    public function getDueDate(): DateTime;

    /**
     * Get all the instructors including those in groups
     * @return ArrayCollection
     */
    public function getAllInstructors(): Collection;

    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface|null
     */
    public function getSession(): ?SessionInterface;

    /**
     * Get the school we belong to
     * @return SchoolInterface|null
     */
    public function getSchool(): ?SchoolInterface;
}
