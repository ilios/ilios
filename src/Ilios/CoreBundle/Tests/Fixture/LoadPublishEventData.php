<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\PublishEvent;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPublishEventData extends AbstractFixture implements
    FixtureInterface,
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
            ->get('ilioscore.dataloader.publishEvent')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new PublishEvent();
            $entity->setId($arr['id']);
            $entity->setMachineIp($arr['machineIp']);
            $manager->persist($entity);
            $this->addReference('publishEvents' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
