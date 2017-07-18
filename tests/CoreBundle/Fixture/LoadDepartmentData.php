<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\Department;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadDepartmentData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\DepartmentData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Department();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setSchool($this->getReference('schools' . $arr['school']));
            $manager->persist($entity);
            $this->addReference('departments' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadSchoolData'
        );
    }
}
