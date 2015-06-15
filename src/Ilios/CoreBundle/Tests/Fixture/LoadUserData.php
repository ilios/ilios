<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements
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
            ->get('ilioscore.dataloader.user')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new User();
            $entity->setId($arr['id']);
            $entity->setFirstName($arr['firstName']);
            $entity->setLastName($arr['lastName']);
            $entity->setMiddleName($arr['middleName']);
            $entity->setEmail($arr['email']);
            $manager->persist($entity);
            $this->addReference('users' . $arr['id'], $entity);
        }

        $manager->flush();
    }

}
