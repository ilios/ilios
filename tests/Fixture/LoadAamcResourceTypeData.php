<?php

namespace App\Tests\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\AamcResourceType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAamcResourceTypeData extends AbstractFixture implements
    ORMFixtureInterface,
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
            ->get('App\Tests\DataLoader\AamcResourceTypeData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new AamcResourceType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setDescription($arr['description']);
            $manager->persist($entity);
            $this->addReference('aamcResourceTypes' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
