<?php

namespace Tests\App\Fixture;

use App\Entity\IngestionException;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadIngestionExceptionData extends AbstractFixture implements
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
            ->get('Tests\AppBundle\DataLoader\IngestionExceptionData')
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
            'Tests\AppBundle\Fixture\LoadUserData',
        );
    }
}
