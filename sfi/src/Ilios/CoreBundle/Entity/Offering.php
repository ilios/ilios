<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class Offering
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="offering",
 *   indexes={
 *     @ORM\Index(name="session_id_k", columns={"session_id"}),
 *     @ORM\Index(name="offering_dates_session_k", columns={"offering_id", "session_id", "start_date", "end_date"})
 *   }
 * )
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class Offering implements OfferingInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @deprecated Replace with trait
     * @var int
     *
     * @ORM\Column(name="offering_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="room", type="string", length=60)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $room;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     *
     * @JMS\Expose
     * @JMS\Type("datetime")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     *
     * @JMS\Expose
     * @JMS\Type("datetime")
     */
    protected $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $deleted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated_on", type="datetime")
     *
     * @JMS\Expose
     * @JMS\Type("datetime")
     */
    protected $lastUpdatedOn;

    /**
     * @var Session
     *
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="offerings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="session_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $session;

    /**
     * @var ArrayCollection|GroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="offerings")
     * @ORM\JoinTable(name="offering_x_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="offering_id", referencedColumnName="offering_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $groups;

    /**
     * @var PublishEventInterface
     *
     * @ORM\ManyToOne(targetEntity="PublishEvent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="publish_event_id", referencedColumnName="publish_event_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $publishEvent;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="InstructorGroup", inversedBy="offerings")
     * @ORM\JoinTable(name="offering_x_instructor_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="offering_id", referencedColumnName="offering_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="instructor_group_id", referencedColumnName="instructor_group_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $instructorGroups;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="offerings")
     * @ORM\JoinTable(name="offering_x_learner",
     *   joinColumns={
     *     @ORM\JoinColumn(name="offering_id", referencedColumnName="offering_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $users;

    /**
     * @var ArrayCollection|RecurringEventInterface[]
     *
     * @ORM\ManyToMany(targetEntity="RecurringEvent", inversedBy="offerings")
     * @ORM\JoinTable(name="offering_x_recurring_event",
     *   joinColumns={
     *     @ORM\JoinColumn(name="offering_id", referencedColumnName="offering_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="recurring_event_id", referencedColumnName="recurring_event_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $recurringEvents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->deleted = false;
        $this->groups = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->recurringEvents = new ArrayCollection();
    }

    /**
     * @param string $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param \DateTime $lastUpdatedOn
     */
    public function setLastUpdatedOn(\DateTime $lastUpdatedOn)
    {
        $this->lastUpdatedOn = $lastUpdatedOn;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdatedOn()
    {
        return $this->lastUpdatedOn;
    }

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function setPublishEvent(PublishEventInterface $publishEvent)
    {
        $this->publishEvent = $publishEvent;
    }

    /**
     * @return PublishEventInterface
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }

    /**
     * @param Collection $groups
     */
    public function setGroups(Collection $groups)
    {
        $this->groups = new ArrayCollection();

        foreach ($groups as $group) {
            $this->addGroup($group);
        }
    }

    /**
     * @param GroupInterface $group
     */
    public function addGroup(GroupInterface $group)
    {
        $this->groups->add($group);
    }

    /**
     * @return ArrayCollection|GroupInterface[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups)
    {
        $this->instructorGroups = new ArrayCollection();

        foreach ($instructorGroups as $instructorGroup) {
            $this->addInstructorGroup($instructorGroup);
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        $this->instructorGroups->add($instructorGroup);
    }

    /**
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups;
    }

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users)
    {
        $this->users = new ArrayCollection();

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user)
    {
        $this->users->add($user);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Collection $recurringEvents
     */
    public function setRecurringEvents(Collection $recurringEvents)
    {
        $this->recurringEvents = new ArrayCollection();

        foreach ($recurringEvents as $recurringEvent) {
            $this->addRecurringEvent($recurringEvent);
        }
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     */
    public function addRecurringEvent(RecurringEventInterface $recurringEvent)
    {
        $this->recurringEvents->add($recurringEvent);
    }

    /**
     * @return ArrayCollection|RecurringEventInterface[]
     */
    public function getRecurringEvents()
    {
        return $this->recurringEvents;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
