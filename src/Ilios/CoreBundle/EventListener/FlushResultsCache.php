<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Doctrine event listener.
 * Flush the cache when changes are made
 *
 * Class FlushResultsCache
 */
class FlushResultsCache
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
        /** @var CacheProvider $resultsCache */
        $resultsCache = $entityManager->getConfiguration()->getResultCacheImpl();
        $resultsCache->deleteAll();
    }
}
