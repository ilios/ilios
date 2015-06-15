<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\UserMadeReminder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserMadeReminderData extends AbstractFixture implements
    FixtureInterface,
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
            ->get('ilioscore.dataloader.userMadeReminder')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new UserMadeReminder();
            $entity->setId($arr['id']);
            $manager->persist($entity);
            $this->addReference('userMadeReminders' . $arr['id'], $entity);
        }

        $manager->flush();
    }

}
