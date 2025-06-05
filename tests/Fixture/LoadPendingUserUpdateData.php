<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\PendingUserUpdate;
use App\Entity\User;
use App\Tests\DataLoader\PendingUserUpdateData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadPendingUserUpdateData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected PendingUserUpdateData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new PendingUserUpdate();
            $entity->setId($arr['id']);
            $entity->setType($arr['type']);
            $entity->setProperty($arr['property']);
            $entity->setValue($arr['value']);
            $entity->setUser($this->getReference('users' . $arr['user'], User::class));

            $manager->persist($entity);
            $this->addReference('pendingUserUpdates' . $arr['id'], $entity);
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
