<?php

namespace Ilios\CoreBundle\Entity\Manager;

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
     * {@inheritdoc}
     */
    public function findPublishEventBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deletePublishEvent(
        PublishEventInterface $publishEvent
    ) {
        $this->em->remove($publishEvent);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createPublishEvent()
    {
        $class = $this->getClass();
        $obj = new $class();
        return $obj;
    }
}
