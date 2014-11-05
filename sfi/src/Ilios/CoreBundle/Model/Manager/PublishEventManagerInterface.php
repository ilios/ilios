<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\PublishEventInterface;

/**
 * Interface PublishEventManagerInterface
 */
interface PublishEventManagerInterface
{
    /** 
     *@return PublishEventInterface
     */
    public function createPublishEvent();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PublishEventInterface
     */
    public function findPublishEventBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return PublishEventInterface[]|Collection
     */
    public function findPublishEventsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param PublishEventInterface $publishEvent
     * @param bool $andFlush
     *
     * @return void
     */
    public function updatePublishEvent(PublishEventInterface $publishEvent, $andFlush = true);

    /**
     * @param PublishEventInterface $publishEvent
     *
     * @return void
     */
    public function deletePublishEvent(PublishEventInterface $publishEvent);

    /**
     * @return string
     */
    public function getClass();
}
