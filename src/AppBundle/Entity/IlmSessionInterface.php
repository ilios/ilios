<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\InstructorGroupsEntityInterface;
use AppBundle\Traits\InstructorsEntityInterface;
use AppBundle\Traits\LearnerGroupsEntityInterface;
use AppBundle\Traits\LearnersEntityInterface;

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

    /**
     * @return string
     */
    public function getHours();

    /**
     * @param \DateTime $dueDate
     */
    public function setDueDate(\DateTime $dueDate = null);

    /**
     * @return \DateTime
     */
    public function getDueDate();

    /**
     * Get all the instructors including those in groups
     * @return ArrayCollection
     */
    public function getAllInstructors();

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface|null
     */
    public function getSession();

    /**
     * Get the school we belong to
     * @return SchoolInterface|null
     */
    public function getSchool();
}
