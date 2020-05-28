<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CourseObjective;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCourseObjectiveData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\CourseObjectiveData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CourseObjective();
            $entity->setId($arr['id']);
            $entity->setPosition($arr['position']);
            $entity->setObjective($this->getReference('objectives' . $arr['objective']));
            $entity->setCourse($this->getReference('courses' . $arr['course']));
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id));
            }
            $manager->persist($entity);

            $this->addReference('courseObjectives' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadObjectiveData',
        ];
    }
}
