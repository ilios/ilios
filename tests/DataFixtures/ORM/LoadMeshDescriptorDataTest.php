<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\MeshDescriptorInterface;
use App\Repository\MeshDescriptorRepository;

/**
 * Class LoadMeshDescriptorDataTest
 */
class LoadMeshDescriptorDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return MeshDescriptorRepository::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'App\DataFixtures\ORM\LoadMeshDescriptorData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadMeshDescriptorData::load
     * @group mesh_data_import
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_descriptor.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshDescriptorInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `mesh_descriptor_uid`,`name`,`annotation`, `deleted`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getName());
        $this->assertEquals($data[2], $entity->getAnnotation());
        $this->assertEquals((bool) $data[3], $entity->isDeleted());
    }
}
