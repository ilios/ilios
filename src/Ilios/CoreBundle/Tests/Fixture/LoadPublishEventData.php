<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\PublishEvent;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPublishEventData extends AbstractFixture implements
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
            ->get('ilioscore.dataloader.publishEvent')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new PublishEvent();
            $entity->setId($arr['id']);
            $entity->setMachineIp('0.0.0.0');
            if (! empty($arr['courses'])) {
                $entity->setTableName('course');
                $entity->setTableRowId($arr['courses'][0]);
            } elseif (! empty($arr['sessions'])) {
                $entity->setTableName('session');
                $entity->setTableRowId($arr['sessions'][0]);
            } elseif (! empty($arr['programYears'])) {
                $entity->setTableName('program_year');
                $entity->setTableRowId($arr['programYears'][0]);
            } elseif (! empty($arr['programs'])) {
                $entity->setTableName('program');
                $entity->setTableRowId($arr['programs'][0]);
            } elseif (! empty($arr['offering'])) {
                $entity->setTableName('offering');
                $entity->setTableRowId($arr['offering'][0]);
            }
            $manager->persist($entity);
            $this->addReference('publishEvents' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
