<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class LoadMeshConceptDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadMeshConceptDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.meshconcept.manager';
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
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadMeshConceptData::load
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
        // `mesh_concept_uid`,`name`,`umls_uid`,`preferred`,`scope_note`,`casn_1_name`,
        // `registry_number`,`created_at`,`updated_at`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getName());
        $this->assertEquals($data[2], $entity->getUmlsUid());
        $this->assertEquals((boolean) $data[3], $entity->getPreferred());
        $this->assertEquals($data[4], $entity->getScopeNote());
        $this->assertEquals($data[5], $entity->getCasn1Name());
        $this->assertEquals($data[6], $entity->getRegistryNumber());
        $this->assertEquals(new \DateTime($data[7], new \DateTimeZone('UTC')), $entity->getCreatedAt());
        $this->assertEquals(new \DateTime($data[8], new \DateTimeZone('UTC')), $entity->getUpdatedAt());
    }
}
