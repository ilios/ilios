<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\LearnerGroupsEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;

/**
 * Interface OfferingInterface
 * @package Ilios\CoreBundle\Entity
 */
interface OfferingInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface,
    TimestampableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface,
    LearnerGroupsEntityInterface
{
    /**
     * @param string $room
     */
    public function setRoom($room);

    /**
     * @return string
     */
    public function getRoom();

    /**
     * @param string $site
     */
    public function setSite($site);

    /**
     * @return string
     */
    public function getSite();

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate);

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate);

    /**
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface|null
     */
    public function getSession();
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
