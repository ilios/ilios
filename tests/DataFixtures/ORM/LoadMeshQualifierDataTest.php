<?php

namespace Tests\App\DataFixtures\ORM;

use App\Entity\MeshQualifierInterface;

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
        return 'AppBundle\Entity\Manager\MeshQualifierManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'AppBundle\DataFixtures\ORM\LoadMeshQualifierData',
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
        // `mesh_qualifier_uid`,`name`,`created_at`,`updated_at`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getName());
        $this->assertEquals(new \DateTime($data[2], new \DateTimeZone('UTC')), $entity->getCreatedAt());
        $this->assertEquals(new \DateTime($data[3], new \DateTimeZone('UTC')), $entity->getUpdatedAt());
    }
}
