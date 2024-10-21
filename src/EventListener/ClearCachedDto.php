<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\DTOCacheManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\PersistentCollection;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ClearCachedDto
{
    public function __construct(
        protected TagAwareCacheInterface $cache,
        protected FeatureManagerInterface $featureManager
    ) {
    }

    /**
     * Clear cache entries referencing any tagged entity
     */
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        if (!$this->featureManager->isActive('dto_caching')) {
            return;
        }
        $objectManager = $eventArgs->getObjectManager();
        $uow = $objectManager->getUnitOfWork();
        $actions = [];

        $actions['update'] = $uow->getScheduledEntityUpdates();
        $actions['delete'] = $uow->getScheduledEntityDeletions();
        $actions['create'] = $uow->getScheduledEntityInsertions();

        $tags = [];
        foreach ($actions as $action => $entities) {
            foreach ($entities as $entity) {
                $entityName = $objectManager->getMetadataFactory()->getMetadataFor($entity::class)->getName();
                if ($action === 'create') {
                    $tags[] = DTOCacheManager::getTag($entityName, false);
                } else {
                    $id = (string) $entity;
                    $tags[] = DTOCacheManager::getTag($entityName, $id);
                }
            }
        }

        $collections = $uow->getScheduledCollectionUpdates();
        /** @var PersistentCollection $col */
        foreach ($collections as $col) {
            foreach ($col->getDeleteDiff() as $entity) {
                $entityName = $objectManager->getMetadataFactory()->getMetadataFor($entity::class)->getName();
                $id = (string) $entity;
                $tags[] = DTOCacheManager::getTag($entityName, $id);
            }
            foreach ($col->getInsertDiff() as $entity) {
                $entityName = $objectManager->getMetadataFactory()->getMetadataFor($entity::class)->getName();
                $id = (string) $entity;
                $tags[] = DTOCacheManager::getTag($entityName, $id);
            }
        }

        $this->cache->invalidateTags($tags);
    }
}
