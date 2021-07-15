<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\MeshDescriptorInterface;
use App\Entity\MeshQualifierInterface;
use App\Repository\MeshDescriptorRepository;

/**
 * Class LoadMeshDescriptorQualifierDataTest
 */
class LoadMeshDescriptorQualifierDataTest extends AbstractDataFixtureTest
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
          'App\DataFixtures\ORM\LoadMeshDescriptorQualifierData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadMeshDescriptorQualifierData::load
     * @group mesh_data_import
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_descriptor_x_qualifier.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshDescriptorInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `mesh_descriptor_uid`,`mesh_qualifier_uid`
        $this->assertEquals($data[0], $entity->getId());
        // find the qualifier
        $qualifierId = $data[1];
        $qualifier = $entity->getQualifiers()->filter(
            fn(MeshQualifierInterface $qualifier) => $qualifier->getId() === $qualifierId
        )->first();
        $this->assertNotEmpty($qualifier);
    }
}
