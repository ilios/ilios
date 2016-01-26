<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\TopicManagerInterface;
use Ilios\CoreBundle\Entity\TopicInterface;

/**
 * @deprecated
 *
 * Class LoadTopicDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadTopicDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.topic.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadTopicData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadTopicData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('topic.csv');
    }

    /**
     * @param array $data
     * @param TopicInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `topic_id`,`title`,`school_id`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        $this->assertEquals($data[2], $entity->getSchool()->getId());
    }

    /**
     * @param array $data
     * @return TopicInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var TopicManagerInterface $em
         */
        $em = $this->em;
        return $em->findTopicBy(['id' => $data[0]]);
    }
}
