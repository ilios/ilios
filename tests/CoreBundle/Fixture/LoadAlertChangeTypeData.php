<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\AlertChangeType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAlertChangeTypeData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\AlertChangeTypeData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new AlertChangeType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            
            $manager->persist($entity);
            $this->addReference('alertChangeTypes' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
