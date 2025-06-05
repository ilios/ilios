<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\InstructorGroup;
use App\Entity\LearnerGroup;
use App\Entity\Offering;
use App\Entity\Session;
use App\Entity\User;
use App\Tests\DataLoader\OfferingData;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadOfferingData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected OfferingData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new Offering();
            $entity->setId($arr['id']);
            $entity->setRoom($arr['room']);
            $entity->setSite($arr['site']);
            if (array_key_exists('url', $arr)) {
                $entity->setUrl($arr['url']);
            }
            $entity->setStartDate(new DateTime($arr['startDate']));
            $entity->setEndDate(new DateTime($arr['endDate']));
            $entity->setSession($this->getReference('sessions' . $arr['session'], Session::class));
            foreach ($arr['learnerGroups'] as $id) {
                $entity->addLearnerGroup($this->getReference('learnerGroups' . $id, LearnerGroup::class));
            }
            foreach ($arr['instructorGroups'] as $id) {
                $entity->addInstructorGroup($this->getReference('instructorGroups' . $id, InstructorGroup::class));
            }
            foreach ($arr['learners'] as $id) {
                $entity->addLearner($this->getReference('users' . $id, User::class));
            }
            foreach ($arr['instructors'] as $id) {
                $entity->addInstructor($this->getReference('users' . $id, User::class));
            }
            $manager->persist($entity);
            $this->addReference('offerings' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadSessionData::class,
            LoadLearnerGroupData::class,
            LoadInstructorGroupData::class,
            LoadUserData::class,
        ];
    }
}
