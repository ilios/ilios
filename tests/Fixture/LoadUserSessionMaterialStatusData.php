<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\SessionLearningMaterial;
use App\Entity\User;
use App\Entity\UserSessionMaterialStatus;
use App\Tests\DataLoader\UserSessionMaterialStatusData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadUserSessionMaterialStatusData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected UserSessionMaterialStatusData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new UserSessionMaterialStatus();
            $entity->setId($arr['id']);
            $entity->setStatus($arr['status']);

            $entity->setUser($this->getReference('users' . $arr['user'], User::class));
            $entity->setMaterial(
                $this->getReference(
                    'sessionLearningMaterials' . $arr['material'],
                    SessionLearningMaterial::class
                )
            );

            $manager->persist($entity);
            $this->addReference('UserSessionMaterialStatuss' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadUserData::class,
            LoadSessionLearningMaterialData::class,
        ];
    }
}
