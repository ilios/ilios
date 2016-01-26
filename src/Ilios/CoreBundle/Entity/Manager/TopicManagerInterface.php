<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\TopicInterface;

/**
 * @deprecated
 * Interface TopicManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface TopicManagerInterface extends ManagerInterface
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
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return TopicInterface[]
     */
    public function findTopicsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param TopicInterface $topic
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateTopic(
        TopicInterface $topic,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param TopicInterface $topic
     *
     * @return void
     */
    public function deleteTopic(
        TopicInterface $topic
    );

    /**
     * @return TopicInterface
     */
    public function createTopic();
}
