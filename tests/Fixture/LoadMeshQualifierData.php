<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\MeshDescriptor;
use App\Entity\MeshQualifier;
use App\Tests\DataLoader\MeshQualifierData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadMeshQualifierData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected MeshQualifierData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new MeshQualifier();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            foreach ($arr['descriptors'] as $id) {
                $entity->addDescriptor($this->getReference('meshDescriptors' . $id, MeshDescriptor::class));
            }
            $this->addReference('meshQualifiers' . $arr['id'], $entity);
            $manager->persist($entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadMeshDescriptorData::class,
        ];
    }
}
