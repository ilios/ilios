<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\MeshQualifierInterface;
use App\Repository\MeshQualifierRepository;

/**
 * Class LoadMeshQualifierDataTest
 */
class LoadMeshQualifierDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return MeshQualifierRepository::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'App\DataFixtures\ORM\LoadMeshQualifierData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadMeshQualifierData::load
     * @group mesh_data_import
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_qualifier.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshQualifierInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `mesh_qualifier_uid`,`name`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getName());
    }
}
