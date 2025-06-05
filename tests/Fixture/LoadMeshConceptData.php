<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\MeshConcept;
use App\Entity\MeshDescriptor;
use App\Tests\DataLoader\MeshConceptData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadMeshConceptData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected MeshConceptData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new MeshConcept();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setPreferred($arr['preferred']);
            $entity->setScopeNote($arr['scopeNote']);
            $entity->setCasn1Name($arr['casn1Name']);
            foreach ($arr['descriptors'] as $id) {
                $entity->addDescriptor($this->getReference('meshDescriptors' . $id, MeshDescriptor::class));
            }
            $this->addReference('meshConcepts' . $arr['id'], $entity);
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
