<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadApplicationConfigData;

/**
 * ApplicationConfig API endpoint Test.
 */
#[\PHPUnit\Framework\Attributes\Group('api_3')]
class ApplicationConfigTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'applicationConfigs';
    protected bool $isGraphQLTestable = false;
    protected bool $enableGetTestsWithServiceToken = false;
    protected bool $enableDeleteTestsWithServiceToken = false;
    protected bool $enablePatchTestsWithServiceToken = false;
    protected bool $enablePostTestsWithServiceToken = false;
    protected bool $enablePutTestsWithServiceToken = false;

    protected function getFixtures(): array
    {
        return [
            LoadApplicationConfigData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'value' => ['value', 'lorem'],
            'name' => ['name', 'ipsum'],
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'name' => [[1], ['name' => 'second name']],
            'value' => [[2], ['value' => 'third value']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
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
                'app_api_applicationconfigs_getone',
                ['version' => $this->apiVersion, 'id' => $data['id']]
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_applicationconfigs_getall',
                ['version' => $this->apiVersion]
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_applicationconfigs_delete',
                ['version' => $this->apiVersion, 'id' => $data['id']]
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_applicationconfigs_post',
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
                'app_api_applicationconfigs_post',
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
                'app_api_applicationconfigs_put',
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
                'app_api_applicationconfigs_patch',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
            json_encode([])
        );
    }
}
