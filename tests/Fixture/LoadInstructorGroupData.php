<?php

namespace Tests\App\Fixture;

use App\Entity\InstructorGroup;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadInstructorGroupData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\AppBundle\DataLoader\InstructorGroupData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new InstructorGroup();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            foreach ($arr['users'] as $id) {
                $entity->addUser($this->getReference('users' . $id));
            }
            if (!empty($arr['school'])) {
                $entity->setSchool($this->getReference('schools' . $arr['school']));
            }
            $manager->persist($entity);
            $this->addReference('instructorGroups' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\AppBundle\Fixture\LoadUserData',
            'Tests\AppBundle\Fixture\LoadSchoolData',
        );
    }
}
