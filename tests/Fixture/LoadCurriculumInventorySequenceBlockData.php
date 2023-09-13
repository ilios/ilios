<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Tests\DataLoader\CurriculumInventorySequenceBlockData;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\CurriculumInventorySequenceBlock;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventorySequenceBlockData extends AbstractFixture implements
    ORMFixtureInterface,
    ContainerAwareInterface,
    DependentFixtureInterface
{
    private ContainerInterface $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get(CurriculumInventorySequenceBlockData::class)
            ->getAll();
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
                $this->getReference('curriculumInventoryAcademicLevels' . $arr['startingAcademicLevel'])
            );
            $entity->setReport($this->getReference('curriculumInventoryReports' . $arr['report']));
            $entity->setEndingAcademicLevel(
                $this->getReference('curriculumInventoryAcademicLevels' . $arr['endingAcademicLevel'])
            );
            $entity->setReport($this->getReference('curriculumInventoryReports' . $arr['report']));
            if (!empty($arr['parent'])) {
                $entity->setParent($this->getReference('curriculumInventorySequenceBlocks' . $arr['parent']));
            }
            if (!empty($arr['course'])) {
                $entity->setCourse($this->getReference('courses' . $arr['course']));
            }
            foreach ($arr['sessions'] as $sessionId) {
                $entity->addSession($this->getReference('sessions' . $sessionId));
            }
            foreach ($arr['excludedSessions'] as $sessionId) {
                $entity->addExcludedSession($this->getReference('sessions' . $sessionId));
            }

            $manager->persist($entity);
            $this->addReference('curriculumInventorySequenceBlocks' . $arr['id'], $entity);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadCurriculumInventoryReportData::class,
            LoadCurriculumInventoryAcademicLevelData::class,
            LoadCourseData::class,
            LoadSessionData::class,
        ];
    }
}
