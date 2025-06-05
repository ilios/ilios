<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Course;
use App\Entity\CourseObjective;
use App\Entity\MeshDescriptor;
use App\Entity\ProgramYearObjective;
use App\Entity\SessionObjective;
use App\Entity\Term;
use App\Tests\DataLoader\CourseObjectiveData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCourseObjectiveData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected CourseObjectiveData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new CourseObjective();
            $entity->setId($arr['id']);
            $entity->setPosition($arr['position']);
            $entity->setActive($arr['active']);
            $entity->setTitle($arr['title']);
            $entity->setCourse($this->getReference('courses' . $arr['course'], Course::class));
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id, Term::class));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id, MeshDescriptor::class));
            }
            if (!empty($arr['ancestor'])) {
                $entity->setAncestor(
                    $this->getReference(
                        'courseObjectives' . $arr['ancestor'],
                        CourseObjective::class
                    )
                );
            }
            foreach ($arr['sessionObjectives'] as $id) {
                $entity->addSessionObjective($this->getReference('sessionObjectives' . $id, SessionObjective::class));
            }
            foreach ($arr['programYearObjectives'] as $id) {
                $entity->addProgramYearObjective(
                    $this->getReference(
                        'programYearObjectives' . $id,
                        ProgramYearObjective::class
                    )
                );
            }

            $manager->persist($entity);

            $this->addReference('courseObjectives' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadMeshDescriptorData::class,
            LoadSessionObjectiveData::class,
            LoadProgramYearObjectiveData::class,
            LoadTermData::class,
            LoadCourseData::class,
        ];
    }
}
