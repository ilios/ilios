<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\MeshDescriptor;
use App\Entity\MeshTree;
use App\Tests\DataLoader\MeshTreeData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

final class LoadMeshTreeData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected MeshTreeData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new MeshTree();
            $entity->setId($arr['id']);
            $entity->setTreeNumber($arr['treeNumber']);
            $entity->setDescriptor($this->getReference('meshDescriptors' . $arr['descriptor'], MeshDescriptor::class));
            $this->addReference('meshTrees' . $arr['treeNumber'], $entity);
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
