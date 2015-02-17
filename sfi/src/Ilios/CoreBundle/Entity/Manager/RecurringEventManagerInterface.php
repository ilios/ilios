<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\RecurringEventInterface;

/**
 * Interface RecurringEventManagerInterface
 * @package Ilios\CoreBundle\Manager
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
     * @return RecurringEventInterface[]|Collection
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
     *
     * @return void
     */
    public function updateRecurringEvent(
        RecurringEventInterface $recurringEvent,
        $andFlush = true
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
