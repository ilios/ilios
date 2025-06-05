<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\MeshDescriptor;
use App\Tests\DataLoader\MeshDescriptorData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadMeshDescriptorData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected MeshDescriptorData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new MeshDescriptor();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setAnnotation($arr['annotation']);
            $entity->setDeleted($arr['deleted']);
            $this->addReference('meshDescriptors' . $arr['id'], $entity);
            $manager->persist($entity);
            $manager->flush();
        }
    }
}
