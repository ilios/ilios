<?php

namespace App\Tests\Fixture;

use App\Entity\Competency;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCompetencyData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\CompetencyData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Competency();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setActive($arr['active']);

            foreach ($arr['aamcPcrses'] as $id) {
                $entity->addAamcPcrs($this->getReference('aamcPcrs' . $id));
            }
            if (isset($arr['parent'])) {
                $entity->setParent($this->getReference('competencies' . $arr['parent']));
            }
            $entity->setSchool($this->getReference('schools' . $arr['school']));

            $manager->persist($entity);
            $this->addReference('competencies' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'App\Tests\Fixture\LoadAamcPcrsData',
            'App\Tests\Fixture\LoadSchoolData',
        );
    }
}
