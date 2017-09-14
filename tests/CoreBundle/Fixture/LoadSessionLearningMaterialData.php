<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\SessionLearningMaterial;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSessionLearningMaterialData extends AbstractFixture implements
    FixtureInterface,
    DependentFixtureInterface,
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
            ->get('Tests\CoreBundle\DataLoader\SessionLearningMaterialData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new SessionLearningMaterial();
            $entity->setId($arr['id']);
            $entity->setRequired($arr['required']);
            $entity->setPublicNotes($arr['publicNotes']);
            $entity->setNotes($arr['notes']);
            $entity->setSession($this->getReference('sessions' . $arr['session']));
            $entity->setPosition($arr['position']);
            if (!is_null($arr['startDate'])) {
                $entity->setStartDate(new \DateTime($arr['startDate']));
            }
            if (!is_null($arr['endDate'])) {
                $entity->setEndDate(new \DateTime($arr['endDate']));
            }
            if ($arr['learningMaterial']) {
                $entity->setLearningMaterial($this->getReference('learningMaterials' . $arr['learningMaterial']));
            }

            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id));
            }
            $manager->persist($entity);
            $this->addReference('sessionLearningMaterials' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData',
        );
    }
}
