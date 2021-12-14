<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Authentication;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAuthenticationData extends AbstractFixture implements
    ORMFixtureInterface,
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
            ->get('App\Tests\DataLoader\AuthenticationData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Authentication();
            $entity->setUsername($arr['username']);
            $entity->setPasswordHash($arr['passwordHash']);
            if (array_key_exists('invalidateTokenIssuedBefore', $arr)) {
                $entity->setInvalidateTokenIssuedBefore($arr['invalidateTokenIssuedBefore']);
            }
            $entity->setUser($this->getReference('users' . $arr['user']));

            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadUserData'
        ];
    }
}
