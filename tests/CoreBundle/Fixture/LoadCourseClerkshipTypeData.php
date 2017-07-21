<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\CourseClerkshipType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCourseClerkshipTypeData extends AbstractFixture implements
    FixtureInterface,
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
            ->get('Tests\CoreBundle\DataLoader\CourseClerkshipTypeData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CourseClerkshipType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $manager->persist($entity);
            $this->addReference('courseClerkshipTypes' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
