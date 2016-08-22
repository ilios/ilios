<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\MeshSemanticType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMeshSemanticTypeData extends AbstractFixture implements
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
            ->get('ilioscore.dataloader.meshSemanticType')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new MeshSemanticType();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $this->addReference('meshSemanticTypes' . $arr['id'], $entity);
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
