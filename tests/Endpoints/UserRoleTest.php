<?php

namespace Tests\App\Endpoints;

use Tests\App\ReadEndpointTest;

/**
 * UserRole API endpoint Test.
 * @group api_4
 */
class UserRoleTest extends ReadEndpointTest
{
    protected $testName =  'userRoles';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\AppBundle\Fixture\LoadUserRoleData',
            'Tests\AppBundle\Fixture\LoadUserData'
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
