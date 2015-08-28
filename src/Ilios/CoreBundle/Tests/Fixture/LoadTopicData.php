<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\Topic;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTopicData extends AbstractFixture implements
    FixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('ilioscore.dataloader.topic')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Topic();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            if (!empty($arr['school'])) {
                $entity->setSchool($this->getReference('schools' . $arr['school']));
            }
            $manager->persist($entity);
            $this->addReference('topics' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
        );
    }
}
