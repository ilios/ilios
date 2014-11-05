<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecurringEvent
 */
class RecurringEvent
{
    /**
     * @var int
     */
    protected $recurringEventId;

    /**
     * @var boolean
     */
    protected $onSunday;

    /**
     * @var boolean
     */
    protected $onMonday;

    /**
     * @var boolean
     */
    protected $onTuesday;

    /**
     * @var boolean
     */
    protected $onWednesday;

    /**
     * @var boolean
     */
    protected $onThursday;

    /**
     * @var boolean
     */
    protected $onFriday;

    /**
     * @var boolean
     */
    protected $onSaturday;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var boolean
     */
    protected $repetitionCount;
    
    /**
     * \Ilios\CoreBundle\Model\RecurringEvent $previousRecurringEvent
     */
    protected $previousRecurringEvent;
    
    /**
     * \Ilios\CoreBundle\Model\RecurringEvent $nextRecurringEvent
     */
    protected $nextRecurringEvent;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $offerings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->offerings = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get recurringEventId
     *
     * @return int 
     */
    public function getRecurringEventId()
    {
        return $this->recurringEventId;
    }

    /**
     * Set onSunday
     *
     * @param boolean $onSunday
     * @return RecurringEvent
     */
    public function setOnSunday($onSunday)
    {
        $this->onSunday = $onSunday;

        return $this;
    }

    /**
     * Get onSunday
     *
     * @return boolean 
     */
    public function getOnSunday()
    {
        return $this->onSunday;
    }

    /**
     * Set onMonday
     *
     * @param boolean $onMonday
     * @return RecurringEvent
     */
    public function setOnMonday($onMonday)
    {
        $this->onMonday = $onMonday;

        return $this;
    }

    /**
     * Get onMonday
     *
     * @return boolean 
     */
    public function getOnMonday()
    {
        return $this->onMonday;
    }

    /**
     * Set onTuesday
     *
     * @param boolean $onTuesday
     * @return RecurringEvent
     */
    public function setOnTuesday($onTuesday)
    {
        $this->onTuesday = $onTuesday;

        return $this;
    }

    /**
     * Get onTuesday
     *
     * @return boolean 
     */
    public function getOnTuesday()
    {
        return $this->onTuesday;
    }

    /**
     * Set onWednesday
     *
     * @param boolean $onWednesday
     * @return RecurringEvent
     */
    public function setOnWednesday($onWednesday)
    {
        $this->onWednesday = $onWednesday;

        return $this;
    }

    /**
     * Get onWednesday
     *
     * @return boolean 
     */
    public function getOnWednesday()
    {
        return $this->onWednesday;
    }

    /**
     * Set onThursday
     *
     * @param boolean $onThursday
     * @return RecurringEvent
     */
    public function setOnThursday($onThursday)
    {
        $this->onThursday = $onThursday;

        return $this;
    }

    /**
     * Get onThursday
     *
     * @return boolean 
     */
    public function getOnThursday()
    {
        return $this->onThursday;
    }

    /**
     * Set onFriday
     *
     * @param boolean $onFriday
     * @return RecurringEvent
     */
    public function setOnFriday($onFriday)
    {
        $this->onFriday = $onFriday;

        return $this;
    }

    /**
     * Get onFriday
     *
     * @return boolean 
     */
    public function getOnFriday()
    {
        return $this->onFriday;
    }

    /**
     * Set onSaturday
     *
     * @param boolean $onSaturday
     * @return RecurringEvent
     */
    public function setOnSaturday($onSaturday)
    {
        $this->onSaturday = $onSaturday;

        return $this;
    }

    /**
     * Get onSaturday
     *
     * @return boolean 
     */
    public function getOnSaturday()
    {
        return $this->onSaturday;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return RecurringEvent
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
     * Set repetitionCount
     *
     * @param boolean $repetitionCount
     * @return RecurringEvent
     */
    public function setRepetitionCount($repetitionCount)
    {
        $this->repetitionCount = $repetitionCount;

        return $this;
    }

    /**
     * Get repetitionCount
     *
     * @return boolean 
     */
    public function getRepetitionCount()
    {
        return $this->repetitionCount;
    }

    /**
     * Set nextRecurringEvent
     *
     * @param \Ilios\CoreBundle\Model\RecurringEvent $recurringEvent
     * @return RecurringEvent
     */
    public function setNextRecurringEvent(\Ilios\CoreBundle\Model\RecurringEvent $recurringEvent = null)
    {
        $this->nextRecurringEvent = $recurringEvent;

        return $this;
    }

    /**
     * Get nextRecurringEvent
     *
     * @return \Ilios\CoreBundle\Model\RecurringEvent 
     */
    public function getNextRecurringEvent()
    {
        return $this->nextRecurringEvent;
    }

    /**
     * Set previousRecurringEvent
     *
     * @param \Ilios\CoreBundle\Model\RecurringEvent $recurringEvent
     * @return RecurringEvent
     */
    public function setPreviousRecurringEvent(\Ilios\CoreBundle\Model\RecurringEvent $recurringEvent = null)
    {
        $this->previousRecurringEvent = $recurringEvent;

        return $this;
    }

    /**
     * Get previousRecurringEvent
     *
     * @return \Ilios\CoreBundle\Model\RecurringEvent 
     */
    public function getPreviousRecurringEvent()
    {
        return $this->previousRecurringEvent;
    }

    /**
     * Add offerings
     *
     * @param \Ilios\CoreBundle\Model\Offering $offerings
     * @return RecurringEvent
     */
    public function addOffering(\Ilios\CoreBundle\Model\Offering $offerings)
    {
        $this->offerings[] = $offerings;

        return $this;
    }

    /**
     * Remove offerings
     *
     * @param \Ilios\CoreBundle\Model\Offering $offerings
     */
    public function removeOffering(\Ilios\CoreBundle\Model\Offering $offerings)
    {
        $this->offerings->removeElement($offerings);
    }

    /**
     * Get offerings
     *
     * @return \Ilios\CoreBundle\Model\Offering[]
     */
    public function getOfferings()
    {
        return $this->offerings->toArray();
    }
}
