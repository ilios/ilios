<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Cohort;
use App\Entity\Course;
use App\Entity\CourseClerkshipType;
use App\Entity\MeshDescriptor;
use App\Entity\School;
use App\Entity\Term;
use App\Entity\User;
use App\Tests\DataLoader\CourseData;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCourseData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected CourseData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
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
            $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));
            if (!empty($arr['ancestor'])) {
                $entity->setAncestor($this->getReference('courses' . $arr['ancestor'], Course::class));
            }
            if (!empty($arr['clerkshipType'])) {
                $entity->setClerkshipType(
                    $this->getReference(
                        'courseClerkshipTypes' . $arr['clerkshipType'],
                        CourseClerkshipType::class
                    )
                );
            }
            foreach ($arr['cohorts'] as $id) {
                $entity->addCohort($this->getReference('cohorts' . $id, Cohort::class));
            }
            foreach ($arr['directors'] as $id) {
                $entity->addDirector($this->getReference('users' . $id, User::class));
            }
            foreach ($arr['administrators'] as $id) {
                $entity->addAdministrator($this->getReference('users' . $id, User::class));
            }
            foreach ($arr['studentAdvisors'] as $id) {
                $entity->addStudentAdvisor($this->getReference('users' . $id, User::class));
            }
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id, Term::class));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id, MeshDescriptor::class));
            }
            $manager->persist($entity);

            $this->addReference('courses' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadCohortData::class,
            LoadSchoolData::class,
            LoadUserData::class,
            LoadTermData::class,
            LoadCourseClerkshipTypeData::class,
            LoadMeshDescriptorData::class,
        ];
    }
}
