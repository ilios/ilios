<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @return string
     */
    public function getSite();

    public function setUrl(?string $url);

    public function getUrl(): ?string;

    public function setStartDate(DateTime $startDate);

    /**
     * @return DateTime
     */
    public function getStartDate();

    public function setEndDate(DateTime $endDate);

    /**
     * @return DateTime
     */
    public function getEndDate();

    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface|null
     */
    public function getSession();

    /**
     * Get all the instructors including those in groups
     * @return ArrayCollection
     */
    public function getAllInstructors();

    /**
     * Returns "alertable" properties in an easy to compare format.
     * @return array.
     */
    public function getAlertProperties();

    /**
     * @return SchoolInterface|null
     */
    public function getSchool();
}
