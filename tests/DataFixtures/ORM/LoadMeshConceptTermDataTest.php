<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\MeshConceptInterface;
use App\Entity\MeshTermInterface;
use App\Repository\MeshConceptRepository;

/**
 * Class LoadMeshConceptTermDataTest
 */
class LoadMeshConceptTermDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return MeshConceptRepository::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'App\DataFixtures\ORM\LoadMeshConceptTermData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadMeshConceptTermData::load
     * @group mesh_data_import
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_concept_x_term.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshConceptInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `mesh_concept_uid`,`mesh_term_id`
        $this->assertEquals($data[0], $entity->getId());
        // find the term
        $termId = (int) $data[1];
        $term = $entity->getTerms()->filter(fn(MeshTermInterface $term) => $term->getId() === $termId)->first();
        $this->assertNotEmpty($term);
    }
}
