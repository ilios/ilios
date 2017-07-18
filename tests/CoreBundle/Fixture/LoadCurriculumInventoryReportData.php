<?php

namespace Tests\CoreBundle\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReport;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventoryReportData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\CurriculumInventoryReportData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryReport();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setDescription($arr['description']);
            $entity->setYear($arr['year']);
            $entity->setStartDate(new \DateTime($arr['startDate']));
            $entity->setEndDate(new \DateTime($arr['endDate']));
            $entity->setProgram($this->getReference('programs' . $arr['program']));
            $entity->generateToken();
            $manager->persist($entity);
            $this->addReference('curriculumInventoryReports' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadProgramData',
        );
    }
}
