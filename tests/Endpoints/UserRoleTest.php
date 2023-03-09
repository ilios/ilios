<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadUserData;
use App\Tests\Fixture\LoadUserRoleData;
use App\Tests\ReadEndpointTest;

/**
 * UserRole API endpoint Test.
 * @group api_4
 */
class UserRoleTest extends ReadEndpointTest
{
    protected string $testName =  'userRoles';
    protected bool $isGraphQLTestable = false;

    protected function getFixtures(): array
    {
        return [
            LoadUserRoleData::class,
            LoadUserData::class
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
            'title' => [[1], ['title' => 'Something Else']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
    }
}
