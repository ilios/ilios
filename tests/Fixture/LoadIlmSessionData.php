<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\IlmSession;
use App\Entity\InstructorGroup;
use App\Entity\LearnerGroup;
use App\Entity\Session;
use App\Entity\User;
use App\Tests\DataLoader\IlmSessionData;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadIlmSessionData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected IlmSessionData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new IlmSession();
            $entity->setId($arr['id']);
            $entity->setHours($arr['hours']);
            $entity->setDueDate(new DateTime($arr['dueDate']));
            $entity->setSession($this->getReference('sessions' . $arr['session'], Session::class));
            foreach ($arr['instructors'] as $id) {
                $entity->addInstructor($this->getReference('users' . $id, User::class));
            }
            foreach ($arr['instructorGroups'] as $id) {
                $entity->addInstructorGroup($this->getReference('instructorGroups' . $id, InstructorGroup::class));
            }
            foreach ($arr['learnerGroups'] as $id) {
                $entity->addLearnerGroup($this->getReference('learnerGroups' . $id, LearnerGroup::class));
            }
            foreach ($arr['learners'] as $id) {
                $entity->addLearner($this->getReference('users' . $id, User::class));
            }
            $manager->persist($entity);
            $this->addReference('ilmSessions' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadUserData::class,
            LoadInstructorGroupData::class,
            LoadLearnerGroupData::class,
            LoadSessionData::class,
        ];
    }
}
