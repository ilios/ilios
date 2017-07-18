<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\IngestionException;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadIngestionExceptionData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\IngestionExceptionData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new IngestionException();
            $entity->setId($arr['id']);
            $entity->setUid($arr['uid']);
            $entity->setUser($this->getReference('users' . $arr['user']));
            $manager->persist($entity);
            $this->addReference('ingestionExceptions' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadUserData',
        );
    }
}
