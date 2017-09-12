<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\MeshDescriptor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMeshDescriptorData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\MeshDescriptorData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new MeshDescriptor();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setAnnotation($arr['annotation']);
            $entity->setDeleted($arr['deleted']);
            $this->addReference('meshDescriptors' . $arr['id'], $entity);
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
