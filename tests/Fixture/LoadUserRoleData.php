<?php

namespace Tests\App\Fixture;

use App\Entity\UserRole;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserRoleData extends AbstractFixture implements
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
            ->get('Tests\AppBundle\DataLoader\UserRoleData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new UserRole();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $manager->persist($entity);
            $this->addReference('userRoles' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
