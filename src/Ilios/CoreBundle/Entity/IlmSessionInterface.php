<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\InstructorGroupsEntityInterface;
use Ilios\CoreBundle\Traits\InstructorsEntityInterface;
use Ilios\CoreBundle\Traits\LearnerGroupsEntityInterface;
use Ilios\CoreBundle\Traits\LearnersEntityInterface;

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
