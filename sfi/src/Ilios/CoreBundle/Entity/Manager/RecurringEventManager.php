<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\RecurringEventInterface;

/**
 * RecurringEvent manager service.
 * Class RecurringEventManager
 * @package Ilios\CoreBundle\Manager
 */
class RecurringEventManager implements RecurringEventManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

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
     * @return RecurringEventInterface[]|Collection
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
     */
    public function updateRecurringEvent(
        RecurringEventInterface $recurringEvent,
        $andFlush = true
    ) {
        $this->em->persist($recurringEvent);
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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
