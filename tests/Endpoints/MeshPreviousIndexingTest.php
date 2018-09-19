<?php

namespace App\Tests\Endpoints;

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
            'App\Tests\Fixture\LoadMeshPreviousIndexingData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadMeshConceptData',
            'App\Tests\Fixture\LoadMeshQualifierData',
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
