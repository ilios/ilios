<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadMeshConceptData;
use App\Tests\Fixture\LoadMeshPreviousIndexingData;
use App\Tests\Fixture\LoadMeshQualifierData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;

/**
 * MeshPreviousIndexing API endpoint Test.
 * @group api_4
 */
class MeshPreviousIndexingTest extends AbstractMeshTest
{
    protected string $testName =  'meshPreviousIndexings';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            LoadMeshPreviousIndexingData::class,
            LoadCourseLearningMaterialData::class,
            LoadSessionLearningMaterialData::class,
            LoadMeshConceptData::class,
            LoadMeshQualifierData::class,
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
