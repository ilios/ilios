<?php

declare(strict_types=1);

namespace App\Service;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\TimestampableEntityInterface;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Exception;

class Timestamper
{
    /**
     * @var array
     */
    protected $entities;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry   = $registry;
        $this->entities   = [];
    }

    /**
     * Add an entity to be time stamped
     * @throws Exception
     */
    public function add(TimestampableEntityInterface $entity, DateTime $timestamp)
    {
        $class = $entity->getClassName();
        $ts = $timestamp->getTimestamp();
        if (!array_key_exists($ts, $this->entities)) {
            $this->entities[$ts] = [];
        }
        if (!array_key_exists($class, $this->entities[$ts])) {
            $this->entities[$ts][$class] = [];
        }
        if (!$entity instanceof IdentifiableEntityInterface) {
            throw new Exception("Tried to timestamp a non identifiable entity {$class}");
        }
        $this->entities[$ts][$class][] = $entity->getId();
    }

    public function flush()
    {
        if ($this->entities !== []) {
            /** @var EntityManager $om */
            $om = $this->registry->getManager();
            foreach ($this->entities as $timestamp => $entities) {
                $dateTime = new DateTime();
                $dateTime->setTimestamp($timestamp);
                foreach ($entities as $class => $ids) {
                    $qb = $om->createQueryBuilder();
                    $qb->update($class, 'c')
                       ->set('c.updatedAt', ':timestamp')
                       ->where($qb->expr()->in('c.id', $ids))
                       ->setParameter('timestamp', $dateTime);
                    $query = $qb->getQuery();
                    $query->execute();
                }
            }

            $this->entities = [];
        }
    }
}
