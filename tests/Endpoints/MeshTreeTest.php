<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadMeshTreeData;

/**
 * MeshTree API endpoint Test.
 * @group api_4
 */
class MeshTreeTest extends AbstractMeshTestCase
{
    protected string $testName =  'meshTrees';

    protected function getFixtures(): array
    {
        return [
            LoadMeshTreeData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'treeNumber' => [[1], ['treeNumber' => 'tree2']],
            'descriptor' => [[0, 1], ['descriptor' => 'abc1']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }
}
