<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\OfferingsEntityInterface;
use Ilios\CoreBundle\Traits\UsersEntityInterface;

/**
 * Interface GroupInterface
 * @package Ilios\CoreBundle\Entity
 */
interface LearnerGroupInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    OfferingsEntityInterface,
    LoggableEntityInterface,
    UsersEntityInterface
{
    /**
     * @param string $location
     */
    public function setLocation($location);

    /**
     * @return string
     */
    public function getLocation();

    /**
     * @param CohortInterface $cohort
     */
    public function setCohort(CohortInterface $cohort);

    /**
     * @return CohortInterface
     */
    public function getCohort();
    /**
     * @param Collection $ilmSessions
     */
    public function setIlmSessions(Collection $ilmSessions);

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function addIlmSession(IlmSessionInterface $ilmSession);

    /**
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getIlmSessions();

    /**
     * @param LearnerGroupInterface $parent
     */
    public function setParent(LearnerGroupInterface $parent);

    /**
     * @return LearnerGroupInterface
     */
    public function getParent();

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * @param LearnerGroupInterface $child
     */
    public function addChild(LearnerGroupInterface $child);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getChildren();

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
     * @param Collection $instructors
     */
    public function setInstructors(Collection $instructors = null);

    /**
     * @param UserInterface $instructor
     */
    public function addInstructor(UserInterface $instructor);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstructors();

    /**
     * Get the school we belong to
     * @return SchoolInterface|null
     */
    public function getSchool();

    /**
     * Gets the program that this learner group belongs to.
     * @return ProgramInterface|null
     */
    public function getProgram();

    /**
     * Gets the program year that this learner group belongs to.
     * @return ProgramYearInterface|null
     */
    public function getProgramYear();
}
