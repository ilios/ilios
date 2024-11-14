<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\UserSessionMaterialStatusInterface;
use App\Tests\Fixture\LoadUserSessionMaterialStatusData;

/**
 * UserSessionMaterialStatusTest API endpoint Test.
 */
#[\PHPUnit\Framework\Attributes\Group('api_1')]
class UserSessionMaterialStatusTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'userSessionMaterialStatuses';
    protected bool $enableGetTestsWithServiceToken = false;
    protected bool $enableDeleteTestsWithServiceToken = false;
    protected bool $enablePatchTestsWithServiceToken = false;
    protected bool $enablePostTestsWithServiceToken = false;
    protected bool $enablePutTestsWithServiceToken = false;

    protected function getFixtures(): array
    {
        return [
            LoadUserSessionMaterialStatusData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'statusStarted' => ['status', UserSessionMaterialStatusInterface::STARTED],
            'statusComplete' => ['status', UserSessionMaterialStatusInterface::COMPLETE],
            'material' => ['material', 2],
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
            'updatedAt' => ['updatedAt', 1, '2015-01-01'],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 2], ['id' => [1, 3]]],
            'status' => [[0], ['status' => UserSessionMaterialStatusInterface::NONE]],
            'statuses' => [[0, 1], ['status' => [
                UserSessionMaterialStatusInterface::NONE,
                UserSessionMaterialStatusInterface::STARTED,
            ]]],
            'material' => [[1], ['material' => 3]],
            'materials' => [[0, 2], ['material' => [1, 5]]],
            'user' => [[0, 1, 2], ['user' => 2]],
            'users' => [[0, 1, 2], ['user' => [2]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 2], ['ids' => [1, 3]]];
        $filters['materials'] = [[0, 2], ['materials' => [1, 5]]];
        $filters['users'] = [[0, 1, 2], ['users' => [2]]];
        $filters['statuses'] = [[0, 1], ['statuses' => [
            UserSessionMaterialStatusInterface::NONE,
            UserSessionMaterialStatusInterface::STARTED,
        ]]];

        return $filters;
    }

    public function testAccessDeniedWithServiceToken(): void
    {
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools(
            $this->kernelBrowser,
            $this->fixtures
        );
        $data = $this->getDataLoader()->getOne();
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_usersessionmaterialstatuses_getone',
                ['version' => $this->apiVersion, 'id' => $data['id']]
            ),
        );
        $this->canNotJsonApi(
            $this->kernelBrowser,
            $jwt,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_usersessionmaterialstatuses_getone',
                ['version' => $this->apiVersion, 'id' => $data['id']]
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_usersessionmaterialstatuses_getall',
                ['version' => $this->apiVersion]
            ),
        );
        $this->canNotJsonApi(
            $this->kernelBrowser,
            $jwt,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_usersessionmaterialstatuses_getall',
                ['version' => $this->apiVersion]
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_usersessionmaterialstatuses_delete',
                ['version' => $this->apiVersion, 'id' => $data['id']]
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_usersessionmaterialstatuses_post',
                ['version' => $this->apiVersion],
            ),
            json_encode([])
        );
        $this->canNotJsonApi(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_usersessionmaterialstatuses_post',
                ['version' => $this->apiVersion],
            ),
            json_encode([])
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_usersessionmaterialstatuses_put',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
            json_encode([])
        );
        $this->canNotJsonApi(
            $this->kernelBrowser,
            $jwt,
            'PATCH',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_usersessionmaterialstatuses_patch',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
            json_encode([])
        );
    }

    protected function getTimeStampFields(): array
    {
        return ['updatedAt'];
    }
}
