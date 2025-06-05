<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Cohort;
use App\Entity\ProgramYear;
use App\Tests\DataLoader\CohortData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCohortData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected CohortData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new Cohort();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setProgramYear($this->getReference('programYears' . $arr['programYear'], ProgramYear::class));
            $manager->persist($entity);
            $this->addReference('cohorts' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadProgramYearData::class,
        ];
    }
}
