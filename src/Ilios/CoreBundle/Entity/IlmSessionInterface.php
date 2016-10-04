<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\LearnerGroupsEntityInterface;

/**
 * Interface IlmSessionInterface
 * @package Ilios\CoreBundle\Entity
 */
interface IlmSessionInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface,
    LearnerGroupsEntityInterface
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
     * @param Collection $instructors
     */
    public function setInstructors(Collection $instructors);

    /**
     * @param UserInterface $instructor
     */
    public function addInstructor(UserInterface $instructor);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstructors();

    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups);

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup);

    /**
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function getInstructorGroups();

    /**
     * Get all the instructors including those in groups
     * @return ArrayCollection
     */
    public function getAllInstructors();

    /**
     * @param Collection $learners
     */
    public function setLearners(Collection $learners);

    /**
     * @param UserInterface $learner
     */
    public function addLearner(UserInterface $learner);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getLearners();

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
