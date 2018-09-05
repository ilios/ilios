<?php

namespace Tests\AppBundle\Fixture;

use AppBundle\Entity\Cohort;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCohortData extends AbstractFixture implements
    ORMFixtureInterface,
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
            ->get('Tests\AppBundle\DataLoader\CohortData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Cohort();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setProgramYear($this->getReference('programYears' . $arr['programYear']));
            $manager->persist($entity);
            $this->addReference('cohorts' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\AppBundle\Fixture\LoadProgramYearData'
        );
    }
}
