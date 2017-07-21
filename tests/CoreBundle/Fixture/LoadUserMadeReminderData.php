<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\UserMadeReminder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserMadeReminderData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\UserMadeReminderData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new UserMadeReminder();
            $entity->setId($arr['id']);
            $entity->setNote($arr['note']);
            $entity->setClosed($arr['closed']);
            $entity->setDueDate(new \DateTime($arr['dueDate']));
            $entity->setUser($this->getReference('users' . $arr['user']));
            
            $manager->persist($entity);
            $this->addReference('userMadeReminders' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadUserData'
        );
    }
}
