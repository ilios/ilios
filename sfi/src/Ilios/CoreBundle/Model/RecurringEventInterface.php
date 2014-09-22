<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface RecurringEventInterface
 */
interface RecurringEventInterface 
{
    public function getRecurringEventId();

    public function setOnSunday($onSunday);

    public function getOnSunday();

    public function setOnMonday($onMonday);

    public function getOnMonday();

    public function setOnTuesday($onTuesday);

    public function getOnTuesday();

    public function setOnWednesday($onWednesday);

    public function getOnWednesday();

    public function setOnThursday($onThursday);

    public function getOnThursday();

    public function setOnFriday($onFriday);

    public function getOnFriday();

    public function setOnSaturday($onSaturday);

    public function getOnSaturday();

    public function setEndDate($endDate);

    public function getEndDate();

    public function setRepetitionCount($repetitionCount);

    public function getRepetitionCount();

    public function setNextRecurringEvent(\Ilios\CoreBundle\Model\RecurringEvent $recurringEvent = null);

    public function getNextRecurringEvent();

    public function setPreviousRecurringEvent(\Ilios\CoreBundle\Model\RecurringEvent $recurringEvent = null);

    public function getPreviousRecurringEvent();

    public function addOffering(\Ilios\CoreBundle\Model\Offering $offerings);

    public function removeOffering(\Ilios\CoreBundle\Model\Offering $offerings);

    public function getOfferings();
}
