<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadLearningMaterialStatusData;
use App\Tests\ReadEndpointTest;

/**
 * LearningMaterialStatus API endpoint Test.
 * @group api_3
 */
class LearningMaterialStatusTest extends ReadEndpointTest
{
    protected string $testName =  'learningMaterialStatuses';

    protected function getFixtures(): array
    {
        return [
            LoadLearningMaterialStatusData::class,
            LoadLearningMaterialData::class
        ];
    }

    /**
     * @inheritDoc
     */
    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[1], ['title' => 'Final']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];

        return $filters;
    }
}
