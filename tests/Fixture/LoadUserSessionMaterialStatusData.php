<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\UserSessionMaterialStatus;
use App\Tests\DataLoader\UserSessionMaterialStatusData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserSessionMaterialStatusData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get(UserSessionMaterialStatusData::class)
            ->getAll();
        foreach ($data as $arr) {
            $entity = new UserSessionMaterialStatus();
            $entity->setId($arr['id']);
            $entity->setStatus($arr['status']);

            $entity->setUser($this->getReference('users' . $arr['user']));
            $entity->setMaterial($this->getReference('sessionLearningMaterials' . $arr['material']));

            $manager->persist($entity);
            $this->addReference('UserSessionMaterialStatuss' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadUserData::class,
            LoadSessionLearningMaterialData::class,
        ];
    }
}
