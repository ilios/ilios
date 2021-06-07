<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\ReadEndpointTest;

/**
 * UserRole API endpoint Test.
 * @group api_4
 */
class UserRoleTest extends ReadEndpointTest
{
    protected string $testName =  'userRoles';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadUserRoleData',
            'App\Tests\Fixture\LoadUserData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'title' => [[1], ['title' => 'Something Else']],
        ];
    }
}
