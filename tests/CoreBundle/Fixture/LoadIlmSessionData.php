<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\IlmSession;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\CoreBundle\DataLoader\IlmSessionData;

class LoadIlmSessionData extends AbstractFixture implements
    FixtureInterface,
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
            ->get(IlmSessionData::class)
            ->getAll();
        foreach ($data as $arr) {
            $entity = new IlmSession();
            $entity->setId($arr['id']);
            $entity->setHours($arr['hours']);
            $entity->setDueDate(new \DateTime($arr['dueDate']));
            $entity->setSession($this->getReference('sessions' . $arr['session']));
            foreach ($arr['instructors'] as $id) {
                $entity->addInstructor($this->getReference('users' . $id));
            }
            foreach ($arr['instructorGroups'] as $id) {
                $entity->addInstructorGroup($this->getReference('instructorGroups' . $id));
            }
            foreach ($arr['learnerGroups'] as $id) {
                $entity->addLearnerGroup($this->getReference('learnerGroups' . $id));
            }
            foreach ($arr['learners'] as $id) {
                $entity->addLearner($this->getReference('users' . $id));
            }
            $manager->persist($entity);
            $this->addReference('ilmSessions' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
        );
    }
}
