<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\MeshTermInterface;

/**
 * Class LoadMeshTermDataTest
 */
class LoadMeshTermDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'App\Entity\Manager\MeshTermManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'App\DataFixtures\ORM\LoadMeshTermData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadMeshTermData::load
     * @group mesh_data_import
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_term.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshTermInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `mesh_term_uid`,`name`,`lexical_tag`,`concept_preferred`,`record_preferred`,
        // `permuted`,`created_at`,`updated_at`,`mesh_term_id`
        $this->assertEquals($data[0], $entity->getMeshTermUid());
        $this->assertEquals($data[1], $entity->getName());
        $this->assertEquals($data[2], $entity->getLexicalTag());
        $this->assertEquals((boolean) $data[3], $entity->isConceptPreferred());
        $this->assertEquals((boolean) $data[4], $entity->isRecordPreferred());
        $this->assertEquals((boolean) $data[5], $entity->isPermuted());
        $this->assertEquals(new \DateTime($data[6], new \DateTimeZone('UTC')), $entity->getCreatedAt());
        $this->assertEquals(new \DateTime($data[7], new \DateTimeZone('UTC')), $entity->getUpdatedAt());
        $this->assertEquals($data[8], $entity->getId());
    }

    /**
     * @inheritdoc
     */
    protected function getEntity(array $data)
    {
        return $this->em->findOneBy(['id' => $data[8]]);
    }
}
