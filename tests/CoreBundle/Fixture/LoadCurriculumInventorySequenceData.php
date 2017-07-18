<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\CurriculumInventorySequence;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventorySequenceData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\CurriculumInventorySequenceData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventorySequence();
            $entity->setId($arr['id']);
            $entity->setDescription($arr['description']);
            $entity->setReport($this->getReference('curriculumInventoryReports' . $arr['report']));
            $manager->persist($entity);
            $this->addReference('curriculumInventorySequences' . $arr['report'], $entity);
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
