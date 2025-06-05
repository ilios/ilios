<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\MeshDescriptor;
use App\Entity\MeshPreviousIndexing;
use App\Tests\DataLoader\MeshPreviousIndexingData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadMeshPreviousIndexingData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected MeshPreviousIndexingData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new MeshPreviousIndexing();
            $entity->setId($arr['id']);
            $entity->setDescriptor($this->getReference('meshDescriptors' . $arr['descriptor'], MeshDescriptor::class));
            $entity->setPreviousIndexing($arr['previousIndexing']);
            $this->addReference('meshPreviousIndexings' . $arr['id'], $entity);
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
