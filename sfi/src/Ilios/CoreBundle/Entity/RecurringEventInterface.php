<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Interface RecurringEventInterface
 * @package Ilios\CoreBundle\Entity
 */
interface RecurringEventInterface
{
    /**
     * @param boolean $onSunday
     */
    public function setOnSunday($onSunday);

    /**
     * @return boolean
     */
    public function isOnSunday();

    /**
     * @param boolean $onMonday
     */
    public function setOnMonday($onMonday);

    /**
     * @return boolean
     */
    public function isOnMonday();

    /**
     * @param boolean $onTuesday
     */
    public function setOnTuesday($onTuesday);

    /**
     * @return boolean
     */
    public function isOnTuesday();

    /**
     * @param boolean $onWednesday
     */
    public function setOnWednesday($onWednesday);

    /**
     * @return boolean
     */
    public function isOnWednesday();

    /**
     * @param boolean $onThursday
     */
    public function setOnThursday($onThursday);

    /**
     * @return boolean
     */
    public function isOnThursday();

    /**
     * @param boolean $onFriday
     */
    public function setOnFriday($onFriday);

    /**
     * @return boolean
     */
    public function isOnFriday();

    /**
     * @param boolean $onSaturday
     */
    public function setOnSaturday($onSaturday);

    /**
     * @return boolean
     */
    public function isOnSaturday();

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate);

    /**
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * @param int $repetitionCount
     */
    public function setRepetitionCount($repetitionCount);

    /**
     * @return int
     */
    public function getRepetitionCount();

    /**
     * @param RecurringEventInterface $recurringEvent
     */
    public function setNextRecurringEvent(RecurringEventInterface $recurringEvent = null);

    /**
     * @return RecurringEventInterface
     */
    public function getNextRecurringEvent();

    /**
     * @param RecurringEventInterface $recurringEvent
     */
    public function setPreviousRecurringEvent(RecurringEventInterface $recurringEvent = null);

    /**
     * @return RecurringEventInterface
     */
    public function getPreviousRecurringEvent();

    /**
     * @param Collection $offerings
     */
    public function setOffering(Collection $offerings);

    /**
     * @param OfferingInterface $offering
     */
    public function addOffering(OfferingInterface $offering);

    /**
     * @return ArrayCollection|OfferingInterface[]
     */
    public function getOfferings();
}