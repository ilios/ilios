<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\UserRole;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserRoles extends AbstractFixture implements
    FixtureInterface,
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
        $userRoles = $this->container->get('ilioscore.dataloader.userroles')->get();
        foreach ($userRoles as $arr) {
            $userRole = new UserRole();
            $userRole->setId($arr['id']);
            $userRole->setTitle($arr['title']);
            $manager->persist($userRole);
            $this->addReference('userRole' . $arr['id'], $userRole);
        }

        $metadata = $manager->getClassMetaData(get_class($userRole));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();

    }
}
