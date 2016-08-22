<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\LearningMaterialStatu;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadLearningMaterialStatuData extends AbstractFixture implements
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
            ->get('ilioscore.dataloader.learningMaterialStatu')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new LearningMaterialStatu();
            $entity->setId($arr['id']);
            $manager->persist($entity);
            $this->addReference('learningMaterialStatus' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
