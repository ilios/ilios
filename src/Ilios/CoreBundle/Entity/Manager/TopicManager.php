<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\TopicInterface;

/**
 * Class TopicManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class TopicManager extends AbstractManager implements TopicManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return TopicInterface
     */
    public function findTopicBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|TopicInterface[]
     */
    public function findTopicsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param TopicInterface $topic
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param TopicInterface $topic
     */
    public function deleteTopic(
        TopicInterface $topic
    ) {
        $this->em->remove($topic);
        $this->em->flush();
    }

    /**
     * @return TopicInterface
     */
    public function createTopic()
    {
        $class = $this->getClass();
        return new $class();
    }
}
