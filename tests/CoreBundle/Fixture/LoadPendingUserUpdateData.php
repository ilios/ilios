<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\PendingUserUpdate;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPendingUserUpdateData extends AbstractFixture implements
    FixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\CoreBundle\DataLoader\PendingUserUpdateData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new PendingUserUpdate();
            $entity->setId($arr['id']);
            $entity->setType($arr['type']);
            $entity->setProperty($arr['property']);
            $entity->setValue($arr['value']);
            $entity->setUser($this->getReference('users' . $arr['user']));

            $manager->persist($entity);
            $this->addReference('pendingUserUpdates' . $arr['id'], $entity);
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
