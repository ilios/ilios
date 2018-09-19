<?php

namespace Tests\App\DataFixtures\ORM;

use App\Entity\MeshConceptInterface;
use App\Entity\MeshTermInterface;

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
        return 'AppBundle\Entity\Manager\MeshConceptManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'AppBundle\DataFixtures\ORM\LoadMeshConceptTermData',
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
        $term = $entity->getTerms()->filter(function (MeshTermInterface $term) use ($termId) {
            return $term->getId() === $termId;
        })->first();
        $this->assertNotEmpty($term);
    }
}
