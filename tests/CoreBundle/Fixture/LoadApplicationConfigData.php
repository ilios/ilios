<?php

namespace Tests\CoreBundle\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ilios\CoreBundle\Entity\ApplicationConfig;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadApplicationConfigData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\ApplicationConfigData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new ApplicationConfig();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setValue($arr['value']);
            $manager->persist($entity);
            $this->addReference('applicationConfigs' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
