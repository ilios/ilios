<?php

namespace Tests\AppBundle\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use AppBundle\Entity\CurriculumInventoryReport;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventoryReportData extends AbstractFixture implements
    ORMFixtureInterface,
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
            ->get('Tests\AppBundle\DataLoader\CurriculumInventoryReportData')
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
            'Tests\AppBundle\Fixture\LoadProgramData',
        );
    }
}
