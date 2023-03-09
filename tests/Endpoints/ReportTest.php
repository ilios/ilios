<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadReportData;
use App\Tests\Fixture\LoadUserData;
use App\Tests\ReadWriteEndpointTest;

/**
 * Report API endpoint Test.
 * @group api_4
 * @group time-sensitive
 */
class ReportTest extends ReadWriteEndpointTest
{
    protected string $testName =  'reports';

    protected function getFixtures(): array
    {
        return [
            LoadReportData::class,
            LoadUserData::class
        ];
    }

    /**
     * @inheritDoc
     */
    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'lorem ipsum'],
            'school' => ['school', 3],
            'subject' => ['subject', 'what'],
            'prepositionalObject' => ['prepositionalObject', 'ever'],
            'prepositionalObjectTableRowId' => ['prepositionalObjectTableRowId', '9'],
            'prepositionalObjectTableRowIdString' => ['prepositionalObjectTableRowId', 'DC123'],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
            'createdAt' => ['createdAt', 1, 99],
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
            'title' => [[1], ['title' => 'second report']],
            'school' => [[2], ['school' => 1]],
            'subject' => [[2], ['subject' => 'subject3']],
            'prepositionalObject' => [[2], ['prepositionalObject' => 'object3']],
            'prepositionalObjectTableRowId' => [[1], ['prepositionalObjectTableRowId' => 14]],
            'prepositionalObjectTableRowIdString' => [[1], ['prepositionalObjectTableRowId' => '14']],
            'user' => [[0, 1, 2], ['user' => 2]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];
        unset($filters['prepositionalObjectTableRowId']);

        return $filters;
    }

    protected function getTimeStampFields(): array
    {
        return ['createdAt'];
    }
}
