<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\RecurringEvent;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadRecurringEventData extends AbstractFixture implements
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
            ->get('ilioscore.dataloader.recurringEvent')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new RecurringEvent();
            $entity->setId($arr['id']);
            $entity->setEndDate(new \DateTime($arr['endDate']));
            $manager->persist($entity);
            $this->addReference('recurringEvents' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
