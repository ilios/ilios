<?php

namespace Tests\AppBundle\Fixture;

use AppBundle\Entity\MeshPreviousIndexing;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMeshPreviousIndexingData extends AbstractFixture implements
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
            ->get('Tests\AppBundle\DataLoader\MeshPreviousIndexingData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new MeshPreviousIndexing();
            $entity->setId($arr['id']);
            $entity->setDescriptor($this->getReference('meshDescriptors' . $arr['descriptor']));
            $entity->setPreviousIndexing($arr['previousIndexing']);
            $this->addReference('meshPreviousIndexings' . $arr['id'], $entity);
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
