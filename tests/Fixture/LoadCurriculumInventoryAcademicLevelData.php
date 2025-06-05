<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CurriculumInventoryAcademicLevel;
use App\Entity\CurriculumInventoryReport;
use App\Tests\DataLoader\CurriculumInventoryAcademicLevelData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCurriculumInventoryAcademicLevelData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected CurriculumInventoryAcademicLevelData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryAcademicLevel();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setLevel($arr['level']);
            $entity->setReport(
                $this->getReference(
                    'curriculumInventoryReports' . $arr['report'],
                    CurriculumInventoryReport::class
                )
            );
            $entity->setDescription($arr['description']);
            $manager->persist($entity);
            $this->addReference('curriculumInventoryAcademicLevels' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadCurriculumInventoryReportData::class,
        ];
    }
}
