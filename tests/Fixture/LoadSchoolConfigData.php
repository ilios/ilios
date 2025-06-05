<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\School;
use App\Tests\DataLoader\SchoolConfigData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\SchoolConfig;

final class LoadSchoolConfigData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected SchoolConfigData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new SchoolConfig();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setValue($arr['value']);
            $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));
            $manager->persist($entity);
            $this->addReference('schoolConfigs' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadSchoolData::class,
        ];
    }
}
