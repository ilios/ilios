<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CourseObjective;
use App\Repository\RepositoryInterface;
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

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('App\Tests\DataLoader\CourseObjectiveData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(CourseObjective::class);
        foreach ($data as $arr) {
            $entity = new CourseObjective();
            $entity->setId($arr['id']);
            $entity->setPosition($arr['position']);
            $entity->setActive($arr['active']);
            $entity->setTitle($arr['title']);
            $entity->setCourse($this->getReference('courses' . $arr['course']));
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id));
            }
            if (array_key_exists('ancestor', $arr)) {
                $entity->setAncestor($this->getReference('courseObjectives' . $arr['ancestor']));
            }
            foreach ($arr['sessionObjectives'] as $id) {
                $entity->addSessionObjective($this->getReference('sessionObjectives' . $id));
            }
            foreach ($arr['programYearObjectives'] as $id) {
                $entity->addProgramYearObjective($this->getReference('programYearObjectives' . $id));
            }

            $repository->update($entity, true, true);

            $this->addReference('courseObjectives' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadMeshDescriptorData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadCourseData',
        ];
    }
}
