<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\RecurringEventInterface;

/**
 * Interface RecurringEventManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface RecurringEventManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return RecurringEventInterface
     */
    public function findRecurringEventBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|RecurringEventInterface[]
     */
    public function findRecurringEventsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param RecurringEventInterface $recurringEvent
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateRecurringEvent(
        RecurringEventInterface $recurringEvent,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param RecurringEventInterface $recurringEvent
     *
     * @return void
     */
    public function deleteRecurringEvent(
        RecurringEventInterface $recurringEvent
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return RecurringEventInterface
     */
    public function createRecurringEvent();
}
