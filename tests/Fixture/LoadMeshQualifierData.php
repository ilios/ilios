<?php

namespace Tests\App\Fixture;

use App\Entity\MeshQualifier;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMeshQualifierData extends AbstractFixture implements
    ORMFixtureInterface,
    ContainerAwareInterface,
    DependentFixtureInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\AppBundle\DataLoader\MeshQualifierData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new MeshQualifier();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            foreach ($arr['descriptors'] as $id) {
                $entity->addDescriptor($this->getReference('meshDescriptors' . $id));
            }
            $this->addReference('meshQualifiers' . $arr['id'], $entity);
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\AppBundle\Fixture\LoadMeshDescriptorData',
        );
    }
}
