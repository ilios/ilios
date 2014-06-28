<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Offering
 */
class Offering
{
    /**
     * @var integer
     */
    private $offeringId;

    /**
     * @var string
     */
    private $room;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var \DateTime
     */
    private $lastUpdatedOn;

    /**
     * @var \Ilios\CoreBundle\Entity\Session
     */
    private $session;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $groups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $instructorGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $reccuringEvents;
    
    /**
     * @var \Ilios\CoreBundle\Entity\PublishEvent
     */
    private $publishEvent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instructorGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reccuringEvents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get offeringId
     *
     * @return integer 
     */
    public function getOfferingId()
    {
        return $this->offeringId;
    }

    /**
     * Set room
     *
     * @param string $room
     * @return Offering
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return string 
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Offering
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Offering
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Offering
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set lastUpdatedOn
     *
     * @param \DateTime $lastUpdatedOn
     * @return Offering
     */
    public function setLastUpdatedOn($lastUpdatedOn)
    {
        $this->lastUpdatedOn = $lastUpdatedOn;

        return $this;
    }

    /**
     * Get lastUpdatedOn
     *
     * @return \DateTime 
     */
    public function getLastUpdatedOn()
    {
        return $this->lastUpdatedOn;
    }

    /**
     * Set session
     *
     * @param \Ilios\CoreBundle\Entity\Session $session
     * @return Offering
     */
    public function setSession(\Ilios\CoreBundle\Entity\Session $session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return \Ilios\CoreBundle\Entity\Session 
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Add groups
     *
     * @param \Ilios\CoreBundle\Entity\Group $groups
     * @return Offering
     */
    public function addGroup(\Ilios\CoreBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Ilios\CoreBundle\Entity\Group $groups
     */
    public function removeGroup(\Ilios\CoreBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Ilios\CoreBundle\Entity\Group[]
     */
    public function getGroups()
    {
        return $this->groups->toArray();
    }

    /**
     * Add instructorGroups
     *
     * @param \Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups
     * @return Offering
     */
    public function addInstructorGroup(\Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups)
    {
        $this->instructorGroups[] = $instructorGroups;

        return $this;
    }

    /**
     * Remove instructorGroups
     *
     * @param \Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups
     */
    public function removeInstructorGroup(\Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups)
    {
        $this->instructorGroups->removeElement($instructorGroups);
    }

    /**
     * Get instructorGroups
     *
     * @return \Ilios\CoreBundle\Entity\InstructorGroup[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups->toArray();
    }

    /**
     * Add users
     *
     * @param \Ilios\CoreBundle\Entity\User $users
     * @return Offering
     */
    public function addUser(\Ilios\CoreBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Ilios\CoreBundle\Entity\User $users
     */
    public function removeUser(\Ilios\CoreBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Ilios\CoreBundle\Entity\User[]
     */
    public function getUsers()
    {
        return $this->users->toArray();
    }

    /**
     * Add reccuringEvents
     *
     * @param \Ilios\CoreBundle\Entity\RecurringEvent $reccuringEvents
     * @return Offering
     */
    public function addReccuringEvent(\Ilios\CoreBundle\Entity\RecurringEvent $reccuringEvents)
    {
        $this->reccuringEvents[] = $reccuringEvents;

        return $this;
    }

    /**
     * Remove reccuringEvents
     *
     * @param \Ilios\CoreBundle\Entity\RecurringEvent $reccuringEvents
     */
    public function removeReccuringEvent(\Ilios\CoreBundle\Entity\RecurringEvent $reccuringEvents)
    {
        $this->reccuringEvents->removeElement($reccuringEvents);
    }

    /**
     * Get reccuringEvents
     *
     * @return \Ilios\CoreBundle\Entity\RecurringEvent[]
     */
    public function getReccuringEvents()
    {
        return $this->reccuringEvents->toArray();
    }

    /**
     * Set publishEvent
     *
     * @param \Ilios\CoreBundle\Entity\PublishEvent $publishEvent
     * @return Offering
     */
    public function setPublishEvent(\Ilios\CoreBundle\Entity\PublishEvent $publishEvent = null)
    {
        $this->publishEvent = $publishEvent;

        return $this;
    }

    /**
     * Get publishEvent
     *
     * @return \Ilios\CoreBundle\Entity\PublishEvent 
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }
}
