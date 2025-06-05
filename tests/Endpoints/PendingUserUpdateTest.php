<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadPendingUserUpdateData;
use App\Tests\Endpoints\PutEndpointTestInterface as PutEndpointInterface;

/**
 * PendingUserUpdate API endpoint Test.
 */
#[Group('api_3')]
final class PendingUserUpdateTest extends AbstractReadEndpoint implements
    PutEndpointInterface,
    DeleteEndpointTestInterface
{
    use PutEndpointTestable;
    use DeleteEndpointTestable;

    protected string $testName =  'pendingUserUpdates';
    protected bool $isGraphQLTestable = false;

    protected function getFixtures(): array
    {
        return [
            LoadPendingUserUpdateData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'type' => ['type', 'something'],
            'property' => ['property', 'else'],
            'value' => ['value', 'nyet'],
            'user' => ['user', 2],
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
            'type' => [[1], ['type' => 'second type']],
            'property' => [[0], ['property' => 'first property']],
            'value' => [[1], ['value' => 'second value']],
            'user' => [[1], ['user' => 4]],
            'users' => [[0], ['users' => [1]]],
            'schools' => [[1], ['schools' => [2]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
    }
}
