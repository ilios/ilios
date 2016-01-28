<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\Topic;
use Ilios\CoreBundle\Entity\TopicInterface;

/**
 * @deprecated
 *
 * Class LoadTopicData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadTopicData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('topic');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }

    /**
     * @return TopicInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new Topic();
    }

    /**
     * @param TopicInterface $entity
     * @param array $data
     * @return TopicInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `topic_id`,`title`,`school_id`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($this->getReference('school' . $data[2]));
        return $entity;
    }
}
