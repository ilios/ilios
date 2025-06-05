<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CurriculumInventoryReport;
use App\Entity\CurriculumInventorySequence;
use App\Tests\DataLoader\CurriculumInventorySequenceData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCurriculumInventorySequenceData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected CurriculumInventorySequenceData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventorySequence();
            $entity->setId($arr['id']);
            $entity->setDescription($arr['description']);
            $entity->setReport(
                $this->getReference(
                    'curriculumInventoryReports' . $arr['report'],
                    CurriculumInventoryReport::class
                )
            );
            $manager->persist($entity);
            $this->addReference('curriculumInventorySequences' . $arr['report'], $entity);
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
