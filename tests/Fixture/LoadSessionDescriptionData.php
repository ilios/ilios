<?php

namespace Tests\App\Fixture;

use App\Entity\SessionDescription;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSessionDescriptionData extends AbstractFixture implements
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
            ->get('Tests\AppBundle\DataLoader\SessionDescriptionData')
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
            'Tests\AppBundle\Fixture\LoadSessionData'
        );
    }
}
