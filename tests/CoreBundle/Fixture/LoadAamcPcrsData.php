<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\AamcPcrs;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAamcPcrsData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\AamcPcrsData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new AamcPcrs();
            $entity->setId($arr['id']);
            $entity->setDescription($arr['description']);
            $manager->persist($entity);
            $this->addReference('aamcPcrs' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
