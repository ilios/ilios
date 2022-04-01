<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DeleteEndpointTestable;
use App\Tests\DeleteEndpointTestInterface;
use App\Tests\Fixture\LoadPendingUserUpdateData;
use App\Tests\PutEndpointTestable;
use App\Tests\PutEndpointTestInterface;
use App\Tests\ReadEndpointTest;

/**
 * PendingUserUpdate API endpoint Test.
 * @group api_3
 */
class PendingUserUpdateTest extends ReadEndpointTest implements PutEndpointTestInterface, DeleteEndpointTestInterface
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

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
    {
        return [
            'type' => ['type', 'something'],
            'property' => ['property', 'else'],
            'value' => ['value', 'nyet'],
            'user' => ['user', 2],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'type' => [[1], ['type' => 'second type']],
            'property' => [[0], ['property' => 'first property']],
            'value' => [[1], ['value' => 'second value']],
            'user' => [[1], ['user' => 4]],
            'users' => [[0], ['users' => [1]]],
            'schools' => [[1], ['schools' => [2]]],
        ];
    }
}
