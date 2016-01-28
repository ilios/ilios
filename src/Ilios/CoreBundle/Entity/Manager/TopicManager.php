<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\TopicInterface;

/**
 * @deprecated
 * Class TopicManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class TopicManager extends AbstractManager implements TopicManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findTopicBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findTopicsBy(
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
    public function updateTopic(
        TopicInterface $topic,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($topic);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($topic));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTopic(
        TopicInterface $topic
    ) {
        $this->em->remove($topic);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createTopic()
    {
        $class = $this->getClass();
        return new $class();
    }
}
