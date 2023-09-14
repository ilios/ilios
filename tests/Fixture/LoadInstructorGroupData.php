<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\InstructorGroup;
use App\Entity\School;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadInstructorGroupData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('App\Tests\DataLoader\InstructorGroupData')
            ->getAll();
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

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadSchoolData',
        ];
    }
}
