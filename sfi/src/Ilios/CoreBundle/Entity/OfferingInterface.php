<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;

/**
 * Interface OfferingInterface
 * @package Ilios\CoreBundle\Entity
 */
interface OfferingInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface
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
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function isDeleted();

    /**
     * @param \DateTime $lastUpdatedOn
     */
    public function setLastUpdatedOn(\DateTime $lastUpdatedOn);

    /**
     * @return \DateTime
     */
    public function getLastUpdatedOn();

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface
     */
    public function getSession();

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function setPublishEvent(PublishEventInterface $publishEvent);

    /**
     * @return PublishEventInterface
     */
    public function getPublishEvent();

    /**
     * @param Collection $groups
     */
    public function setGroups(Collection $groups);

    /**
     * @param GroupInterface $group
     */
    public function addGroup(GroupInterface $group);

    /**
     * @return ArrayCollection|GroupInterface[]
     */
    public function getGroups();

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
     * @param Collection $users
     */
    public function setUsers(Collection $users);

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers();

    /**
     * @param Collection $recurringEvents
     */
    public function setRecurringEvents(Collection $recurringEvents);

    /**
     * @param RecurringEventInterface $recurringEvent
     */
    public function addRecurringEvent(RecurringEventInterface $recurringEvent);

    /**
     * @return ArrayCollection|RecurringEventInterface[]
     */
    public function getRecurringEvents();
}
