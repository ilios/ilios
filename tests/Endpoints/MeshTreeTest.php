<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadMeshTreeData;

/**
 * MeshTree API endpoint Test.
 */
#[Group('api_4')]
class MeshTreeTest extends AbstractMeshEndpoint
{
    protected string $testName =  'meshTrees';

    protected function getFixtures(): array
    {
        return [
            LoadMeshTreeData::class,
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
            'treeNumber' => [[1], ['treeNumber' => 'tree2']],
            'descriptor' => [[0, 1], ['descriptor' => 'abc1']],
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
