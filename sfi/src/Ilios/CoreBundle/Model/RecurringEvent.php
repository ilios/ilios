<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecurringEvent
 */
class RecurringEvent
{
    /**
     * @var integer
     */
    private $recurringEventId;

    /**
     * @var boolean
     */
    private $onSunday;

    /**
     * @var boolean
     */
    private $onMonday;

    /**
     * @var boolean
     */
    private $onTuesday;

    /**
     * @var boolean
     */
    private $onWednesday;

    /**
     * @var boolean
     */
    private $onThursday;

    /**
     * @var boolean
     */
    private $onFriday;

    /**
     * @var boolean
     */
    private $onSaturday;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var boolean
     */
    private $repetitionCount;
    
    /**
     * \Ilios\CoreBundle\Entity\RecurringEvent $previousRecurringEvent
     */
    private $previousRecurringEvent;
    
    /**
     * \Ilios\CoreBundle\Entity\RecurringEvent $nextRecurringEvent
     */
    private $nextRecurringEvent;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $offerings;

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
     * @return integer 
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
     * @param \Ilios\CoreBundle\Entity\RecurringEvent $recurringEvent
     * @return RecurringEvent
     */
    public function setNextRecurringEvent(\Ilios\CoreBundle\Entity\RecurringEvent $recurringEvent = null)
    {
        $this->nextRecurringEvent = $recurringEvent;

        return $this;
    }

    /**
     * Get nextRecurringEvent
     *
     * @return \Ilios\CoreBundle\Entity\RecurringEvent 
     */
    public function getNextRecurringEvent()
    {
        return $this->nextRecurringEvent;
    }

    /**
     * Set previousRecurringEvent
     *
     * @param \Ilios\CoreBundle\Entity\RecurringEvent $recurringEvent
     * @return RecurringEvent
     */
    public function setPreviousRecurringEvent(\Ilios\CoreBundle\Entity\RecurringEvent $recurringEvent = null)
    {
        $this->previousRecurringEvent = $recurringEvent;

        return $this;
    }

    /**
     * Get previousRecurringEvent
     *
     * @return \Ilios\CoreBundle\Entity\RecurringEvent 
     */
    public function getPreviousRecurringEvent()
    {
        return $this->previousRecurringEvent;
    }

    /**
     * Add offerings
     *
     * @param \Ilios\CoreBundle\Entity\Offering $offerings
     * @return RecurringEvent
     */
    public function addOffering(\Ilios\CoreBundle\Entity\Offering $offerings)
    {
        $this->offerings[] = $offerings;

        return $this;
    }

    /**
     * Remove offerings
     *
     * @param \Ilios\CoreBundle\Entity\Offering $offerings
     */
    public function removeOffering(\Ilios\CoreBundle\Entity\Offering $offerings)
    {
        $this->offerings->removeElement($offerings);
    }

    /**
     * Get offerings
     *
     * @return \Ilios\CoreBundle\Entity\Offering[]
     */
    public function getOfferings()
    {
        return $this->offerings->toArray();
    }
}
