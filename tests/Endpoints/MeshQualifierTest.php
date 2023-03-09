<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadMeshQualifierData;

/**
 * MeshQualifier API endpoint Test.
 * @group api_1
 * @group time-sensitive
 */
class MeshQualifierTest extends AbstractMeshTestCase
{
    protected string $testName =  'meshQualifiers';

    protected function getFixtures(): array
    {
        return [
            LoadMeshQualifierData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function readOnlyPropertiesToTest()
    {
        return [
            'createdAt' => ['createdAt', 1, 99],
            'updatedAt' => ['updatedAt', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => '1']],
            'ids' => [[0, 1], ['id' => ['1', '2']]],
            'name' => [[1], ['name' => 'second qualifier']],
            'descriptors' => [[0, 1], ['descriptors' => ['abc1']]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => ['1', '2']]];

        return $filters;
    }

    public function getTimeStampFields(): array
    {
        return ['createdAt', 'updatedAt'];
    }
}
