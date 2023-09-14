<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\UserSessionMaterialStatusInterface;
use App\Tests\Fixture\LoadUserSessionMaterialStatusData;

/**
 * UserSessionMaterialStatusTest API endpoint Test.
 * @group api_1
 */
class UserSessionMaterialStatusTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'userSessionMaterialStatuses';

    protected function getFixtures(): array
    {
        return [
            LoadUserSessionMaterialStatusData::class,
        ];
    }

    public function putsToTest(): array
    {
        return [
            'statusStarted' => ['status', UserSessionMaterialStatusInterface::STARTED],
            'statusComplete' => ['status', UserSessionMaterialStatusInterface::COMPLETE],
            'material' => ['material', 2],
        ];
    }

    public function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
            'updatedAt' => ['updatedAt', 1, '2015-01-01'],
        ];
    }

    public function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 2], ['id' => [1, 3]]],
            'status' => [[0], ['status' => UserSessionMaterialStatusInterface::NONE]],
            'statuses' => [[0, 1], ['status' => [
                UserSessionMaterialStatusInterface::NONE,
                UserSessionMaterialStatusInterface::STARTED
            ]]],
            'material' => [[1], ['material' => 3]],
            'materials' => [[0, 2], ['material' => [1, 5]]],
            'user' => [[0, 1, 2], ['user' => 2]],
            'users' => [[0, 1, 2], ['user' => [2]]],
        ];
    }

    public function graphQLFiltersToTest(): array
    {
        $filters = $this->filtersToTest();
        $filters['ids'] = [[0, 2], ['ids' => [1, 3]]];
        $filters['materials'] = [[0, 2], ['materials' => [1, 5]]];
        $filters['users'] = [[0, 1, 2], ['users' => [2]]];
        $filters['statuses'] = [[0, 1], ['statuses' => [
            UserSessionMaterialStatusInterface::NONE,
            UserSessionMaterialStatusInterface::STARTED
        ]]];

        return $filters;
    }

    protected function getTimeStampFields(): array
    {
        return ['updatedAt'];
    }
}
