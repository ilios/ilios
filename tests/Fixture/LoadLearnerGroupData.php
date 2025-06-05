<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Cohort;
use App\Entity\InstructorGroup;
use App\Entity\LearnerGroup;
use App\Entity\User;
use App\Tests\DataLoader\LearnerGroupData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadLearnerGroupData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected LearnerGroupData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new LearnerGroup();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            if (!empty($arr['location'])) {
                $entity->setUrl($arr['url']);
            }
            $entity->setNeedsAccommodation($arr['needsAccommodation']);

            if (!empty($arr['location'])) {
                $entity->setLocation($arr['location']);
            }
            if (!empty($arr['parent'])) {
                $entity->setParent($this->getReference('learnerGroups' . $arr['parent'], LearnerGroup::class));
            }
            if (!empty($arr['ancestor'])) {
                $entity->setAncestor($this->getReference('learnerGroups' . $arr['ancestor'], LearnerGroup::class));
            }
            $entity->setCohort($this->getReference('cohorts' . $arr['cohort'], Cohort::class));
            foreach ($arr['users'] as $id) {
                $entity->addUser($this->getReference('users' . $id, User::class));
            }
            foreach ($arr['instructors'] as $id) {
                $entity->addInstructor($this->getReference('users' . $id, User::class));
            }
            foreach ($arr['instructorGroups'] as $id) {
                $entity->addInstructorGroup($this->getReference('instructorGroups' . $id, InstructorGroup::class));
            }
            $manager->persist($entity);
            $this->addReference('learnerGroups' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadCohortData::class,
            LoadUserData::class,
            LoadInstructorGroupData::class,
        ];
    }
}
