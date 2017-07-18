<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\LearningMaterialUserRole;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadLearningMaterialUserRoleData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\LearningMaterialUserRoleData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new LearningMaterialUserRole();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $manager->persist($entity);
            $this->addReference('learningMaterialUserRoles' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
