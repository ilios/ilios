<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Program;
use App\Tests\DataLoader\CurriculumInventoryReportData;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\CurriculumInventoryReport;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCurriculumInventoryReportData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected CurriculumInventoryReportData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryReport();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setDescription($arr['description']);
            $entity->setYear($arr['year']);
            $entity->setStartDate(new DateTime($arr['startDate']));
            $entity->setEndDate(new DateTime($arr['endDate']));
            $entity->setProgram($this->getReference('programs' . $arr['program'], Program::class));
            $entity->generateToken();
            $manager->persist($entity);
            $this->addReference('curriculumInventoryReports' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadProgramData::class,
        ];
    }
}
