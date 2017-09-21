<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class LoadMeshConceptDataTest
 */
class LoadMeshConceptDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'Ilios\CoreBundle\Entity\Manager\MeshConceptManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshConceptData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadMeshConceptData::load
     * @group mesh_data_import
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_concept.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshConceptInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `mesh_concept_uid`,`name`,`preferred`,`scope_note`,`casn_1_name`,
        // `registry_number`,`created_at`,`updated_at`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getName());
        $this->assertEquals((boolean) $data[2], $entity->getPreferred());
        $this->assertEquals($data[3], $entity->getScopeNote());
        $this->assertEquals($data[4], $entity->getCasn1Name());
        $this->assertEquals($data[5], $entity->getRegistryNumber());
        $this->assertEquals(new \DateTime($data[6], new \DateTimeZone('UTC')), $entity->getCreatedAt());
        $this->assertEquals(new \DateTime($data[7], new \DateTimeZone('UTC')), $entity->getUpdatedAt());
    }
}
