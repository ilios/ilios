<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\PublishEventInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Interface PublishEventManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 *
 * @deprecated
 */
interface PublishEventManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PublishEventInterface
     */
    public function findPublishEventBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|PublishEventInterface[]
     */
    public function findPublishEventsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param PublishEventInterface $publishEvent
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updatePublishEvent(
        PublishEventInterface $publishEvent,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param PublishEventInterface $publishEvent
     *
     * @return void
     */
    public function deletePublishEvent(
        PublishEventInterface $publishEvent
    );

    /**
     * @return PublishEventInterface
     */
    public function createPublishEvent();
}
