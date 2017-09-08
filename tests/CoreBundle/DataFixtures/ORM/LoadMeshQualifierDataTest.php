<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\MeshQualifierInterface;

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
        return 'Ilios\CoreBundle\Entity\Manager\MeshQualifierManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshQualifierData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadMeshQualifierData::load
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
