<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Authentication;
use App\Entity\User;
use App\Service\SessionUserProvider;
use App\Tests\DataLoader\AuthenticationData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class LoadAuthenticationData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(
        protected UserPasswordHasherInterface $passwordHasher,
        protected SessionUserProvider $sessionUserProvider,
        protected AuthenticationData $authenticationData,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->authenticationData->getAll();
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

    public function getDependencies(): array
    {
        return [
            LoadUserData::class,
        ];
    }
}
