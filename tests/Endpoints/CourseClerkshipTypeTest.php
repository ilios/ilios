<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadCourseClerkshipTypeData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\ReadWriteEndpointTestCase;

/**
 * CourseClerkshipType API endpoint Test.
 * @group api_4
 */
class CourseClerkshipTypeTest extends ReadWriteEndpointTestCase
{
    protected string $testName =  'courseClerkshipTypes';

    protected function getFixtures(): array
    {
        return [
            LoadCourseClerkshipTypeData::class,
            LoadCourseData::class
        ];
    }

    /**
     * @inheritDoc
     */
    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'too much salt!'],
            'courses' => ['courses', [3]],
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
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'title' => [[1], ['title' => 'second clerkship type']],
            'courses' => [[0], ['courses' => [1]]],
        ];
    }


    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }
}
