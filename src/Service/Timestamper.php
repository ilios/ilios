<?php

declare(strict_types=1);

namespace App\Service;

use App\Traits\TimestampableEntityInterface;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

class Timestamper
{
    protected array $entities = [];

    public function __construct(protected ManagerRegistry $registry)
    {
    }

    /**
     * Add an entity to be time stamped
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
        // When and entity has already been deleted it will lose it's ID, so we need to record it here
        $this->entities[$ts][$class][] = (string) $entity;
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
                    if ($ids !== []) {
                        $qb = $om->createQueryBuilder();
                        $qb->update($class, 'c')
                            ->set('c.updatedAt', ':timestamp')
                            ->where($qb->expr()->in('c.id', $ids))
                            ->setParameter('timestamp', $dateTime);
                        $query = $qb->getQuery();
                        $query->execute();
                    }
                }
            }

            $this->entities = [];
        }
    }
}
