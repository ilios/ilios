<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadUserData;
use App\Tests\Fixture\LoadUserRoleData;

/**
 * UserRole API endpoint Test.
 */
#[Group('api_4')]
class UserRoleTest extends AbstractReadEndpoint
{
    protected string $testName =  'userRoles';
    protected bool $isGraphQLTestable = false;

    protected function getFixtures(): array
    {
        return [
            LoadUserRoleData::class,
            LoadUserData::class,
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
            'title' => [[1], ['title' => 'Something Else']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
    }
}
