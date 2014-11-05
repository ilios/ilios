<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\RecurringEventInterface;

/**
 * Interface RecurringEventManagerInterface
 */
interface RecurringEventManagerInterface
{
    /** 
     *@return RecurringEventInterface
     */
    public function createRecurringEvent();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return RecurringEventInterface
     */
    public function findRecurringEventBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return RecurringEventInterface[]|Collection
     */
    public function findRecurringEventsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param RecurringEventInterface $recurringEvent
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateRecurringEvent(RecurringEventInterface $recurringEvent, $andFlush = true);

    /**
     * @param RecurringEventInterface $recurringEvent
     *
     * @return void
     */
    public function deleteRecurringEvent(RecurringEventInterface $recurringEvent);

    /**
     * @return string
     */
    public function getClass();
}
