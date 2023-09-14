<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Course;
use App\Repository\RepositoryInterface;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCourseData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\CourseData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(Course::class);
        foreach ($data as $arr) {
            $entity = new Course();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setLevel($arr['level']);
            $entity->setYear($arr['year']);
            $entity->setStartDate(new DateTime($arr['startDate']));
            $entity->setEndDate(new DateTime($arr['endDate']));
            $entity->setExternalId($arr['externalId']);
            $entity->setLocked($arr['locked']);
            $entity->setArchived($arr['archived']);
            $entity->setPublishedAsTbd($arr['publishedAsTbd']);
            $entity->setPublished($arr['published']);
            $entity->setSchool($this->getReference('schools' . $arr['school']));
            if (!empty($arr['ancestor'])) {
                $entity->setAncestor($this->getReference('courses' . $arr['ancestor']));
            }
            if (!empty($arr['clerkshipType'])) {
                $entity->setClerkshipType($this->getReference('courseClerkshipTypes' . $arr['clerkshipType']));
            }
            foreach ($arr['cohorts'] as $id) {
                $entity->addCohort($this->getReference('cohorts' . $id));
            }
            foreach ($arr['directors'] as $id) {
                $entity->addDirector($this->getReference('users' . $id));
            }
            foreach ($arr['administrators'] as $id) {
                $entity->addAdministrator($this->getReference('users' . $id));
            }
            foreach ($arr['studentAdvisors'] as $id) {
                $entity->addStudentAdvisor($this->getReference('users' . $id));
            }
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id));
            }
            $repository->update($entity, true, true);

            $this->addReference('courses' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadCohortData',
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadCourseClerkshipTypeData',
            'App\Tests\Fixture\LoadMeshDescriptorData',
        ];
    }
}
