<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class RecurringEvent
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="recurring_event")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class RecurringEvent implements RecurringEventInterface
{
    use StringableIdEntity;
    /**
     * @var int
     *
     * @ORM\Column(name="recurring_event_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var boolean
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @ORM\Column(name="on_sunday", type="boolean")
     */
    protected $onSunday;

    /**
     * @var boolean
     *
     * @ORM\Column(name="on_monday", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="integer")     
     */
    protected $onMonday;

    /**
     * @var boolean
     *
     * @ORM\Column(name="on_tuesday", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="integer")     
     */
    protected $onTuesday;

    /**
     * @var boolean
     *
     * @ORM\Column(name="on_wednesday", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="integer")     
     */
    protected $onWednesday;

    /**
     * @var boolean
     *
     * @ORM\Column(name="on_thursday", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="integer")     
     */
    protected $onThursday;

    /**
     * @var boolean
     *
     * @ORM\Column(name="on_friday", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="integer")     
     */
    protected $onFriday;

    /**
     * @var boolean
     *
     * @ORM\Column(name="on_saturday", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="integer")     
     */
    protected $onSaturday;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     *
     * @Assert\NotBlank()
     */
    protected $endDate;

    /**
     * @var int
     *
     * @ORM\Column(name="repetition_count", type="smallint", nullable=true)
     *
     * @Assert\Type(type="integer")
     */
    protected $repetitionCount;

    /**
     * @var RecurringEventInterface
     *
     * @ORM\ManyToOne(targetEntity="RecurringEvent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="previous_recurring_event_id", referencedColumnName="recurring_event_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("previousRecurringEvent")
     */
    protected $previousRecurringEvent;

    /**
     * @var RecurringEventInterface
     * @
     * @ORM\ManyToOne(targetEntity="RecurringEvent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="next_recurring_event_id", referencedColumnName="recurring_event_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("nextRecurringEvent")
     */
    protected $nextRecurringEvent;

    /**
     * @var ArrayCollection|RecurringEventInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="recurringEvents")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $offerings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->offerings = new ArrayCollection();
    }

    /**
     * @param boolean $onSunday
     */
    public function setOnSunday($onSunday)
    {
        $this->onSunday = $onSunday;
    }

    /**
     * @return boolean
     */
    public function isOnSunday()
    {
        return $this->onSunday;
    }

    /**
     * @param boolean $onMonday
     */
    public function setOnMonday($onMonday)
    {
        $this->onMonday = $onMonday;
    }

    /**
     * @return boolean
     */
    public function isOnMonday()
    {
        return $this->onMonday;
    }

    /**
     * @param boolean $onTuesday
     */
    public function setOnTuesday($onTuesday)
    {
        $this->onTuesday = $onTuesday;
    }

    /**
     * @return boolean
     */
    public function isOnTuesday()
    {
        return $this->onTuesday;
    }

    /**
     * @param boolean $onWednesday
     */
    public function setOnWednesday($onWednesday)
    {
        $this->onWednesday = $onWednesday;
    }

    /**
     * @return boolean
     */
    public function isOnWednesday()
    {
        return $this->onWednesday;
    }

    /**
     * @param boolean $onThursday
     */
    public function setOnThursday($onThursday)
    {
        $this->onThursday = $onThursday;
    }

    /**
     * @return boolean
     */
    public function isOnThursday()
    {
        return $this->onThursday;
    }

    /**
     * @param boolean $onFriday
     */
    public function setOnFriday($onFriday)
    {
        $this->onFriday = $onFriday;
    }

    /**
     * @return boolean
     */
    public function isOnFriday()
    {
        return $this->onFriday;
    }

    /**
     * @param boolean $onSaturday
     */
    public function setOnSaturday($onSaturday)
    {
        $this->onSaturday = $onSaturday;
    }

    /**
     * @return boolean
     */
    public function isOnSaturday()
    {
        return $this->onSaturday;
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
     * @param int $repetitionCount
     */
    public function setRepetitionCount($repetitionCount)
    {
        $this->repetitionCount = $repetitionCount;
    }

    /**
     * @return int
     */
    public function getRepetitionCount()
    {
        return $this->repetitionCount;
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     */
    public function setNextRecurringEvent(RecurringEventInterface $recurringEvent = null)
    {
        $this->nextRecurringEvent = $recurringEvent;
    }

    /**
     * @return RecurringEventInterface
     */
    public function getNextRecurringEvent()
    {
        return $this->nextRecurringEvent;
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     */
    public function setPreviousRecurringEvent(RecurringEventInterface $recurringEvent = null)
    {
        $this->previousRecurringEvent = $recurringEvent;
    }

    /**
     * @return RecurringEventInterface
     */
    public function getPreviousRecurringEvent()
    {
        return $this->previousRecurringEvent;
    }

    /**
     * @param Collection $offerings
     */
    public function setOffering(Collection $offerings)
    {
        $this->offerings = new ArrayCollection();

        foreach ($offerings as $offering) {
            $this->addOffering($offering);
        }
    }

    /**
     * @param OfferingInterface $offering
     */
    public function addOffering(OfferingInterface $offering)
    {
        $this->offerings->add($offering);
    }

    /**
     * @param Collection $offerings
     */
    public function setOfferings(Collection $offerings)
    {
        $this->offerings = new ArrayCollection();

        foreach ($offerings as $offering) {
            $this->addOffering($offering);
        }
    }

    /**
     * @return ArrayCollection|OfferingInterface[]
     */
    public function getOfferings()
    {
        return $this->offerings;
    }
}
