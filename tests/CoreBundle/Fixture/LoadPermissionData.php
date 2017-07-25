<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\Permission;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadProgramData
 */
class LoadPermissionData extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface,
    DependentFixtureInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\CoreBundle\DataLoader\PermissionData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Permission();
            $entity->setId($arr['id']);
            $entity->setUser($this->getReference('users' . $arr['user']));
            $entity->setTableName($arr['tableName']);
            $entity->setTableRowId($arr['tableRowId']);
            $entity->setCanRead($arr['canRead']);
            $entity->setCanWrite($arr['canWrite']);
            $manager->persist($entity);
            $this->addReference('permissions' . $arr['id'], $entity);
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
