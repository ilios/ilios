<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Users extends AbstractFixture implements
    FixtureInterface,
    // DependentFixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->container->get('ilioscore.dataloader.users')->get();
        foreach ($users as $arr) {
            $user = new User();
            $user->setId($arr['id']);
            $user->setFirstName($arr['firstName']);
            $user->setLastName($arr['lastName']);
            $user->setEmail($arr['email']);

            $manager->persist($user);
            $this->addReference('user' + $arr['id'], $user);
        }

        //We have to disable auto id generation in order to save with ID
        $metadata = $manager->getClassMetaData(get_class($user));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();

    }

    // public function getDependencies()
    // {
    //     return array(
    //         'Ilios\CoreBundle\Tests\Fixtures\LoadSchoolData'
    //     );
    // }
}
