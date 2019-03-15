<?php

namespace App\Tests\Fixture;

use App\Entity\ProgramYear;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProgramYearData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\ProgramYearData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new ProgramYear();
            $entity->setId($arr['id']);
            $entity->setStartYear($arr['startYear']);
            $entity->setLocked($arr['locked']);
            $entity->setArchived($arr['archived']);
            $entity->setProgram($this->getReference('programs' . $arr['program']));
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id));
            }
            foreach ($arr['objectives'] as $id) {
                $entity->addObjective($this->getReference('objectives' . $id));
            }

            foreach ($arr['competencies'] as $id) {
                $entity->addCompetency($this->getReference('competencies'.$id));
            }
            $manager->persist($entity);
            $this->addReference('programYears' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'App\Tests\Fixture\LoadProgramData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadObjectiveData',
            'App\Tests\Fixture\LoadCompetencyData',
        );
    }
}
