<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Program;
use App\Entity\School;
use App\Tests\DataLoader\ProgramData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadProgramData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected ProgramData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new Program();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setShortTitle($arr['shortTitle']);
            $entity->setDuration($arr['duration']);
            $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));
            $manager->persist($entity);
            $this->addReference('programs' . $arr['id'], $entity);
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
