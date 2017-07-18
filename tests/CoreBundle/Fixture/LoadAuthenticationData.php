<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\Authentication;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAuthenticationData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\AuthenticationData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Authentication();
            $entity->setUsername($arr['username']);
            $entity->setPasswordSha256($arr['passwordSha256']);
            $entity->setPasswordBcrypt($arr['passwordBcrypt']);
            $entity->setUser($this->getReference('users' . $arr['user']));

            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadUserData'
        );
    }
}
