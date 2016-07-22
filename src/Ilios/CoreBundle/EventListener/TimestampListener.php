<?php

namespace Ilios\CoreBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;

use Ilios\CoreBundle\Traits\TimestampableEntityInterface;

class TimestampListener
{
     public function onFlush(OnFlushEventArgs $eventArgs)
     {
        $entityManager = $eventArgs->getEntityManager();
        $uow = $entityManager->getUnitOfWork();
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );
        
        foreach ($entities as $entity) {
            if ($entity instanceof TimestampableEntityInterface) {
                $entity->stampUpdate();
                $classMetadata = $entityManager->getClassMetadata(get_class($entity));
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
    }
}
