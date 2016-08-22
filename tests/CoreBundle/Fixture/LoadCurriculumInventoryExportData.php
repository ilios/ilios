<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\CurriculumInventoryExport;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventoryExportData extends AbstractFixture implements
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
            ->get('ilioscore.dataloader.curriculumInventoryExport')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryExport();
            $entity->setId($arr['id']);
            $entity->setReport($this->getReference('curriculumInventoryReports' . $arr['report']));
            $entity->setCreatedBy($this->getReference('users' .$arr['createdBy']));
            $entity->setDocument($arr['document']);
            $manager->persist($entity);
            $this->addReference('curriculumInventoryExports' . $arr['report'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
        );
    }
}
