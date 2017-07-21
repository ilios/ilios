<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\SessionDescription;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSessionDescriptionData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\SessionDescriptionData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new SessionDescription();
            $entity->setId($arr['id']);
            $entity->setSession($this->getReference('sessions' . $arr['session']));
            $entity->setDescription($arr['description']);
            $manager->persist($entity);
            $this->addReference('sessionDescriptions' . $arr['session'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadSessionData'
        );
    }
}
