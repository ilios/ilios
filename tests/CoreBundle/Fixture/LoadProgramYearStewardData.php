<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\ProgramYearSteward;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProgramYearStewardData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\ProgramYearStewardData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new ProgramYearSteward();
            $entity->setId($arr['id']);
            $entity->setSchool($this->getReference('schools' . $arr['school']));
            $entity->setDepartment($this->getReference('departments' . $arr['department']));
            $entity->setProgramYear($this->getReference('programYears' . $arr['programYear']));
            
            $manager->persist($entity);
            $this->addReference('programYearStewards' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadDepartmentData',
        );
    }
}
