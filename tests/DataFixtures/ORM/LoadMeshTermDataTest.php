<?php

declare(strict_types=1);

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
        // `mesh_term_uid`,`name`,`lexical_tag`,`concept_preferred`,`record_preferred`, `permuted`, `mesh_term_id`
        $this->assertEquals($data[0], $entity->getMeshTermUid());
        $this->assertEquals($data[1], $entity->getName());
        $this->assertEquals($data[2], $entity->getLexicalTag());
        $this->assertEquals((bool) $data[3], $entity->isConceptPreferred());
        $this->assertEquals((bool) $data[4], $entity->isRecordPreferred());
        $this->assertEquals((bool) $data[5], $entity->isPermuted());
        $this->assertEquals($data[6], $entity->getId());
    }

    /**
     * @inheritdoc
     */
    protected function getEntity(array $data)
    {
        return $this->em->findOneBy(['id' => $data[6]]);
    }
}
