<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\PublishEventInterface;

/**
 * Class PublishEventManager
 * @package Ilios\CoreBundle\Entity\Manager
 *
 * @deprecated
 */
class PublishEventManager extends AbstractManager implements PublishEventManagerInterface
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
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

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
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param PublishEventInterface $publishEvent
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updatePublishEvent(
        PublishEventInterface $publishEvent,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($publishEvent);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($publishEvent));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function deletePublishEvent(
        PublishEventInterface $publishEvent
    ) {
        $this->em->remove($publishEvent);
        $this->em->flush();
    }

    /**
     * @return PublishEventInterface
     */
    public function createPublishEvent()
    {
        $class = $this->getClass();
        $obj = new $class();
        return $obj;
    }
}
