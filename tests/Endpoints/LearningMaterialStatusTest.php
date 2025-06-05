<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadLearningMaterialStatusData;

/**
 * LearningMaterialStatus API endpoint Test.
 */
#[Group('api_3')]
final class LearningMaterialStatusTest extends AbstractReadEndpoint
{
    protected string $testName =  'learningMaterialStatuses';

    protected function getFixtures(): array
    {
        return [
            LoadLearningMaterialStatusData::class,
            LoadLearningMaterialData::class,
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
            'title' => [[1], ['title' => 'Final']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];
        $filters['missingIds'] = [[], ['ids' => [99]]];

        return $filters;
    }
}
