<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\InstructorGroup;
use App\Entity\School;
use App\Entity\User;
use App\Tests\DataLoader\InstructorGroupData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadInstructorGroupData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected InstructorGroupData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new InstructorGroup();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            foreach ($arr['users'] as $id) {
                $entity->addUser($this->getReference('users' . $id, User::class));
            }
            if (!empty($arr['school'])) {
                $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));
            }
            $manager->persist($entity);
            $this->addReference('instructorGroups' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadUserData::class,
            LoadSchoolData::class,
        ];
    }
}
