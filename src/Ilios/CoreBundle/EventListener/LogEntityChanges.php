<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\PersistentCollection;
use Ilios\CoreBundle\Entity\LoggableEntityInterface;
use Ilios\CoreBundle\Service\LoggerQueue;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Doctrine event listener.
 * Listen for every change to an entity and log it.
 *
 * Class LogEntityChanges
 */
class LogEntityChanges
{
    //We have to inject the container to avoid a circular service reference
    use ContainerAwareTrait;

    /**
    * Get all the entities that have changed and create log entries for them
    *
    * @param OnFlushEventArgs $eventArgs
    */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $uow = $entityManager->getUnitOfWork();
        $actions = [];
        
        $actions['create'] = $uow->getScheduledEntityInsertions();
        $actions['update'] = $uow->getScheduledEntityUpdates();
        $actions['delete'] = $uow->getScheduledEntityDeletions();

        $updates = [];
        foreach ($actions as $action => $entities) {
            foreach ($entities as $entity) {
                if ($entity instanceof LoggableEntityInterface) {
                    $changes = $uow->getEntityChangeSet($entity);
                    $updates[get_class($entity)] = [
                        'entity' => $entity,
                        'action' => $action,
                        'changes' => array_keys($changes)
                    ];
                }
            }
        }

        $collections = $uow->getScheduledCollectionUpdates();
        foreach ($collections as $col) {
            /** @var $col PersistentCollection */
            $entity = $col->getOwner();
            $change = $col->getTypeClass()->name;
            if ($entity instanceof LoggableEntityInterface) {
                $entityClass = get_class($entity);
                if (!array_key_exists($entityClass, $updates)) {
                    $updates[$entityClass] = [
                        'entity' => $entity,
                        'action' => 'update',
                        'changes' => []
                    ];
                }
                $ref = new \ReflectionClass($change);
                $updates[$entityClass]['changes'][] = 'Ref:' . $ref->getShortName();
            }
        }
        $collections = $uow->getScheduledCollectionDeletions();
        foreach ($collections as $col) {
            /** @var $col PersistentCollection */
            $entity = $col->getOwner();
            $change = $col->getTypeClass()->name;
            if ($entity instanceof LoggableEntityInterface) {
                $entityClass = get_class($entity);
                if (!array_key_exists($entityClass, $updates)) {
                    $updates[$entityClass] = [
                        'entity' => $entity,
                        'action' => 'update',
                        'changes' => []
                    ];
                }
                $ref = new \ReflectionClass($change);
                $updates[$entityClass]['changes'][] = 'Ref:' . $ref->getShortName();
            }
        }
        $loggerQueue = $this->container->get(LoggerQueue::class);
        foreach ($updates as $arr) {
            $valuesChanged = implode($arr['changes'], ',');
            $loggerQueue->add($arr['action'], $arr['entity'], $valuesChanged);
        }
    }
}
