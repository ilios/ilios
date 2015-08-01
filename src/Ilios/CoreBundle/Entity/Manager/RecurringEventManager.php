<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\RecurringEventInterface;

/**
 * Class RecurringEventManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class RecurringEventManager extends AbstractManager implements RecurringEventManagerInterface
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
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

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
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateRecurringEvent(
        RecurringEventInterface $recurringEvent,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($recurringEvent);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($recurringEvent));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     */
    public function deleteRecurringEvent(
        RecurringEventInterface $recurringEvent
    ) {
        $this->em->remove($recurringEvent);
        $this->em->flush();
    }

    /**
     * @return RecurringEventInterface
     */
    public function createRecurringEvent()
    {
        $class = $this->getClass();
        return new $class();
    }
}
