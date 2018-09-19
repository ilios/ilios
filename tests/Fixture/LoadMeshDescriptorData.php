<?php

namespace Tests\App\Fixture;

use App\Entity\MeshDescriptor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMeshDescriptorData extends AbstractFixture implements
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
            ->get('Tests\AppBundle\DataLoader\MeshDescriptorData')
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
