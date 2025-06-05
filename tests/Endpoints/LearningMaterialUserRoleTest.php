<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadLearningMaterialUserRoleData;

/**
 * LearningMaterialUserRole API endpoint Test.
 */
#[Group('api_1')]
final class LearningMaterialUserRoleTest extends AbstractReadEndpoint
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
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
            'title' => [[1], ['title' => 'second lm user role']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];
        $filters['missingIds'] = [[], ['ids' => [99]]];

        return $filters;
    }
}
