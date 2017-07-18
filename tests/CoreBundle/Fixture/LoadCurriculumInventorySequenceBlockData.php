<?php

namespace Tests\CoreBundle\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventorySequenceBlockData extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface,
    DependentFixtureInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\CoreBundle\DataLoader\CurriculumInventorySequenceBlockData')
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
            $entity->setStartDate(new \DateTime($arr['startDate']));
            $entity->setEndDate(new \DateTime($arr['endDate']));
            $entity->setAcademicLevel($this->getReference('curriculumInventoryAcademicLevels' . $arr['academicLevel']));
            $entity->setReport($this->getReference('curriculumInventoryReports' . $arr['report']));
            if (!empty($arr['parent'])) {
                $entity->setParent($this->getReference('curriculumInventorySequenceBlocks' . $arr['parent']));
            }
            foreach ($arr['sessions'] as $sessionId) {
                $entity->addSession($this->getReference('sessions'. $sessionId));
            }
            $manager->persist($entity);
            $this->addReference('curriculumInventorySequenceBlocks' . $arr['id'], $entity);
        }
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
        );
    }
}
