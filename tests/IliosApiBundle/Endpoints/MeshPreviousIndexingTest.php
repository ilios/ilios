<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * MeshPreviousIndexing API endpoint Test.
 * @group api_4
 */
class MeshPreviousIndexingTest extends AbstractMeshTest
{
    protected $testName =  'meshPreviousIndexings';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshPreviousIndexingData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadMeshConceptData',
            'Tests\CoreBundle\Fixture\LoadMeshQualifierData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'descriptor' => [[1], ['descriptor' => 'abc2']],
            'previousIndexing' => [[1], ['previousIndexing' => 'second previous indexing']],
        ];
    }
}
