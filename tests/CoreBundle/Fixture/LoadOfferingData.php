<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\Offering;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadOfferingData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\OfferingData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Offering();
            $entity->setId($arr['id']);
            $entity->setRoom($arr['room']);
            $entity->setSite($arr['site']);
            $entity->setStartDate(new \DateTime($arr['startDate']));
            $entity->setEndDate(new \DateTime($arr['endDate']));
            $entity->setSession($this->getReference('sessions' . $arr['session']));
            foreach ($arr['learnerGroups'] as $id) {
                $entity->addLearnerGroup($this->getReference('learnerGroups' . $id));
            }
            foreach ($arr['instructorGroups'] as $id) {
                $entity->addInstructorGroup($this->getReference('instructorGroups' . $id));
            }
            foreach ($arr['learners'] as $id) {
                $entity->addLearner($this->getReference('users' . $id));
            }
            foreach ($arr['instructors'] as $id) {
                $entity->addInstructor($this->getReference('users' . $id));
            }
            $manager->persist($entity);
            $this->addReference('offerings' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
            'Tests\CoreBundle\Fixture\LoadUserData',
        ];
    }
}
