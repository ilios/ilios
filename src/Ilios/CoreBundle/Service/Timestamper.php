<?php

namespace Ilios\CoreBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;

class Timestamper
{
    /**
     * @var array
     */
    protected $entities;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry   = $registry;
        $this->entities   = [];
    }

    /**
     * Add an entity to be time stamped
     * @param TimestampableEntityInterface $entity
     * @throws \Exception
     */
    public function add(TimestampableEntityInterface $entity)
    {
        $class = $entity->getClassName();
        if (!array_key_exists($class, $this->entities)) {
            $this->entities[$class] = [];
        }
        if (!$entity instanceof IdentifiableEntityInterface) {
            throw new \Exception("Tried to timestamp a non identifiable entity {$class}");
        }
        $this->entities[$class][] = $entity->getId();
    }

    public function flush()
    {
        if (count($this->entities)) {
            $om = $this->registry->getManager();
            $now = new \DateTime();
            foreach ($this->entities as $class => $ids) {
                $qb = $om->createQueryBuilder();
                $qb->update($class, 'c')
                    ->set('c.updatedAt', ':now')
                    ->where($qb->expr()->in('c.id', $ids))
                    ->setParameter('now', $now);
                $query = $qb->getQuery();
                $query->execute();
            }
            $this->entities = [];
        }
    }
}
