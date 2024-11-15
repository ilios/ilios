<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadMeshConceptData;
use App\Tests\Fixture\LoadMeshPreviousIndexingData;
use App\Tests\Fixture\LoadMeshQualifierData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;

/**
 * MeshPreviousIndexing API endpoint Test.
 */
#[Group('api_4')]
class MeshPreviousIndexingTest extends AbstractMeshEndpoint
{
    protected string $testName =  'meshPreviousIndexings';

    protected function getFixtures(): array
    {
        return [
            LoadMeshPreviousIndexingData::class,
            LoadCourseLearningMaterialData::class,
            LoadSessionLearningMaterialData::class,
            LoadMeshConceptData::class,
            LoadMeshQualifierData::class,
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'descriptor' => [[1], ['descriptor' => 'abc2']],
            'previousIndexing' => [[1], ['previousIndexing' => 'second previous indexing']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }
}
