<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Course;
use App\Entity\CurriculumInventoryAcademicLevel;
use App\Entity\CurriculumInventoryReport;
use App\Entity\Session;
use App\Tests\DataLoader\CurriculumInventorySequenceBlockData;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\CurriculumInventorySequenceBlock;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCurriculumInventorySequenceBlockData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected CurriculumInventorySequenceBlockData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventorySequenceBlock();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            if (array_key_exists('description', $arr)) {
                $entity->setDescription($arr['description']);
            }
            $entity->setTrack($arr['track']);
            $entity->setChildSequenceOrder($arr['childSequenceOrder']);
            $entity->setOrderInSequence($arr['orderInSequence']);
            $entity->setMinimum($arr['minimum']);
            $entity->setMaximum($arr['maximum']);
            $entity->setDuration($arr['duration']);
            $entity->setRequired($arr['required']);
            $entity->setStartDate(new DateTime($arr['startDate']));
            $entity->setEndDate(new DateTime($arr['endDate']));
            $entity->setStartingAcademicLevel(
                $this->getReference(
                    'curriculumInventoryAcademicLevels' . $arr['startingAcademicLevel'],
                    CurriculumInventoryAcademicLevel::class
                )
            );
            $entity->setReport(
                $this->getReference(
                    'curriculumInventoryReports' . $arr['report'],
                    CurriculumInventoryReport::class
                )
            );
            $entity->setEndingAcademicLevel(
                $this->getReference(
                    'curriculumInventoryAcademicLevels' . $arr['endingAcademicLevel'],
                    CurriculumInventoryAcademicLevel::class
                )
            );
            $entity->setReport(
                $this->getReference(
                    'curriculumInventoryReports' . $arr['report'],
                    CurriculumInventoryReport::class
                )
            );
            if (!empty($arr['parent'])) {
                $entity->setParent(
                    $this->getReference(
                        'curriculumInventorySequenceBlocks' . $arr['parent'],
                        CurriculumInventorySequenceBlock::class
                    )
                );
            }
            if (!empty($arr['course'])) {
                $entity->setCourse($this->getReference('courses' . $arr['course'], Course::class));
            }
            foreach ($arr['sessions'] as $sessionId) {
                $entity->addSession($this->getReference('sessions' . $sessionId, Session::class));
            }
            foreach ($arr['excludedSessions'] as $sessionId) {
                $entity->addExcludedSession($this->getReference('sessions' . $sessionId, Session::class));
            }

            $manager->persist($entity);
            $this->addReference('curriculumInventorySequenceBlocks' . $arr['id'], $entity);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LoadCurriculumInventoryReportData::class,
            LoadCurriculumInventoryAcademicLevelData::class,
            LoadCourseData::class,
            LoadSessionData::class,
        ];
    }
}
