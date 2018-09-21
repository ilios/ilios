<?php

namespace App\Tests\Fixture;

use App\Entity\ProgramYearSteward;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProgramYearStewardData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\ProgramYearStewardData')
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
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadDepartmentData',
        );
    }
}
