<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\RecurringEventManager as BaseRecurringEventManager;
use Ilios\CoreBundle\Model\RecurringEventInterface;

class RecurringEventManager extends BaseRecurringEventManager
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
    public function findRecurringEventBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return RecurringEventInterface[]|Collection
     */
    public function findRecurringEventsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateRecurringEvent(RecurringEventInterface $recurringEvent, $andFlush = true)
    {
        $this->em->persist($recurringEvent);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     *
     * @return void
     */
    public function deleteRecurringEvent(RecurringEventInterface $recurringEvent)
    {
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
}
