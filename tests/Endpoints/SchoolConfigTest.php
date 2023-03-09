<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadSchoolConfigData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\ReadWriteEndpointTestCase;

/**
 * SchoolConfig API endpoint Test.
 * @group api_5
 */
class SchoolConfigTest extends ReadWriteEndpointTestCase
{
    protected string $testName =  'schoolConfigs';
    protected bool $isGraphQLTestable = false;

    protected function getFixtures(): array
    {
        return [
            LoadSchoolData::class,
            LoadSchoolConfigData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function putsToTest(): array
    {
        return [
            'value' => ['value', 'lorem'],
            'name' => ['name', 'ipsum'],
            'school' => ['school', 2],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function readOnlyPropertiesToTest(): array
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'name' => [[1], ['name' => 'second config']],
            'value' => [[2], ['value' => 'third value']],
            'school' => [[2], ['school' => 2]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
    }
}
