<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventoryAcademicLevelData extends AbstractFixture implements
    FixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\CoreBundle\DataLoader\CurriculumInventoryAcademicLevelData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryAcademicLevel();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setLevel($arr['level']);
            $entity->setReport($this->getReference('curriculumInventoryReports' . $arr['report']));
            $entity->setDescription($arr['description']);
            $manager->persist($entity);
            $this->addReference('curriculumInventoryAcademicLevels' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
        );
    }
}
