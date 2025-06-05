<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CurriculumInventoryExport;
use App\Entity\CurriculumInventoryReport;
use App\Entity\User;
use App\Tests\DataLoader\CurriculumInventoryExportData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCurriculumInventoryExportData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected CurriculumInventoryExportData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryExport();
            $entity->setId($arr['id']);
            $entity->setReport(
                $this->getReference(
                    'curriculumInventoryReports' . $arr['report'],
                    CurriculumInventoryReport::class
                )
            );
            $entity->setCreatedBy($this->getReference('users' . $arr['createdBy'], User::class));
            $entity->setDocument($arr['document']);
            $manager->persist($entity);
            $this->addReference('curriculumInventoryExports' . $arr['report'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadUserData::class,
            LoadProgramYearData::class,
            LoadCurriculumInventoryReportData::class,
        ];
    }
}
