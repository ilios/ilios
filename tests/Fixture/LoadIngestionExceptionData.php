<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\IngestionException;
use App\Entity\User;
use App\Tests\DataLoader\IngestionExceptionData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadIngestionExceptionData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected IngestionExceptionData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new IngestionException();
            $entity->setId($arr['id']);
            $entity->setUid($arr['uid']);
            $entity->setUser($this->getReference('users' . $arr['user'], User::class));
            $manager->persist($entity);
            $this->addReference('ingestionExceptions' . $arr['id'], $entity);
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
