<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\MeshConcept;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMeshConceptData extends AbstractFixture implements
    FixtureInterface,
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
            ->get('Tests\CoreBundle\DataLoader\MeshConceptData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new MeshConcept();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setPreferred($arr['preferred']);
            $entity->setScopeNote($arr['scopeNote']);
            $entity->setCasn1Name($arr['casn1Name']);
            $entity->setRegistryNumber($arr['registryNumber']);
            foreach ($arr['descriptors'] as $id) {
                $entity->addDescriptor($this->getReference('meshDescriptors' . $id));
            }
            $this->addReference('meshConcepts' . $arr['id'], $entity);
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData',
        );
    }
}
