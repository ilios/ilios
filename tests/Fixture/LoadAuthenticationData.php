<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Authentication;
use App\Entity\User;
use App\Service\SessionUserProvider;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoadAuthenticationData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{
    private $container;

    public function __construct(
        protected UserPasswordHasherInterface $passwordHasher,
        protected SessionUserProvider $sessionUserProvider
    ) {
    }

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('App\Tests\DataLoader\AuthenticationData')
            ->getAll();
        foreach ($data as $arr) {
            $user = $this->getReference('users' . $arr['user'], User::class);
            $entity = new Authentication();
            $entity->setUsername($arr['username']);
            $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);
            $hashedPassword = $this->passwordHasher->hashPassword($sessionUser, $arr['password']);
            $entity->setPasswordHash($hashedPassword);
            if (array_key_exists('invalidateTokenIssuedBefore', $arr)) {
                $entity->setInvalidateTokenIssuedBefore($arr['invalidateTokenIssuedBefore']);
            }
            $entity->setUser($user);

            $manager->persist($entity);
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadUserData'
        ];
    }
}
