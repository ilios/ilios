<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\MeshQualifier;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMeshQualifierData extends AbstractFixture implements
    ORMFixtureInterface,
    ContainerAwareInterface,
    DependentFixtureInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('App\Tests\DataLoader\MeshQualifierData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(MeshQualifier::class);
        foreach ($data as $arr) {
            $entity = new MeshQualifier();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            foreach ($arr['descriptors'] as $id) {
                $entity->addDescriptor($this->getReference('meshDescriptors' . $id));
            }
            $this->addReference('meshQualifiers' . $arr['id'], $entity);
            $repository->update($entity, false, true);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadMeshDescriptorData',
        ];
    }
}
