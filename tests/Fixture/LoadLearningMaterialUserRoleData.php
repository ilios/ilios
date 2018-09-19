<?php

namespace Tests\App\Fixture;

use App\Entity\LearningMaterialUserRole;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadLearningMaterialUserRoleData extends AbstractFixture implements
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
            ->get('Tests\AppBundle\DataLoader\LearningMaterialUserRoleData')
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
