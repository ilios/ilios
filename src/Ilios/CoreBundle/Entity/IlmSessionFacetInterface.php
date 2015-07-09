<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface IlmSessionFacetInterface
 * @package Ilios\CoreBundle\Entity
 */
interface IlmSessionFacetInterface extends IdentifiableEntityInterface
{
    /**
     * @param string $hours
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
     * @param Collection $groups
     */
    public function setLearnerGroups(Collection $learnerGroups);

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getLearnerGroups();

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
     * @return SessionInterface
     */
    public function getSession();
}
