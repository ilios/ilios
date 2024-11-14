<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadLearningMaterialUserRoleData;

/**
 * LearningMaterialUserRole API endpoint Test.
 */
#[\PHPUnit\Framework\Attributes\Group('api_1')]
class LearningMaterialUserRoleTest extends AbstractReadEndpoint
{
    protected string $testName =  'learningMaterialUserRoles';

    protected function getFixtures(): array
    {
        return [
            LoadLearningMaterialUserRoleData::class,
            LoadLearningMaterialData::class,
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'title' => [[1], ['title' => 'second lm user role']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }
}
