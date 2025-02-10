<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadAamcMethodData;
use App\Tests\Fixture\LoadSessionTypeData;

/**
 * AamcMethod API endpoint Test.
 */
#[Group('api_1')]
class AamcMethodTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'aamcMethods';
    protected bool $enableDeleteTestsWithServiceToken = false;
    protected bool $enablePatchTestsWithServiceToken = false;
    protected bool $enablePostTestsWithServiceToken = false;
    protected bool $enablePutTestsWithServiceToken = false;

    protected function getFixtures(): array
    {
        return [
            LoadAamcMethodData::class,
            LoadSessionTypeData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'description' => ['description', 'lorem ipsum'],
            'sessionTypes' => ['sessionTypes', [1]],
            // 'id' => ['id', 'NEW1'], // skipped
            'active' => ['active', false],
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 'AM001']],
            'ids' => [[0, 1], ['id' => ['AM001', 'AM002']]],
            'missingId' => [[], ['id' => 'nothing']],
            'missingIds' => [[], ['id' => ['nothing']]],
            'description' => [[1], ['description' => 'filterable description']],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
            'active' => [[0], ['active' => true]],
            'notActive' => [[1], ['active' => false]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => ['AM001', 'AM002']]];
        $filters['missingIds'] = [[], ['ids' => ['nothing']]];

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
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_aamcmethods_delete',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_aamcmethods_post',
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
                'app_api_aamcmethods_post',
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
                'app_api_aamcmethods_put',
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
                'app_api_aamcmethods_patch',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
            json_encode([])
        );
    }

    public function testPutReadOnly(
        ?string $key = null,
        mixed $id = null,
        mixed $value = null,
        bool $skipped = false
    ): void {
        $this->markTestSkipped('test not applicable');
    }

    public function testPutReadOnlyWithServiceToken(
        ?string $key = null,
        mixed $id = null,
        mixed $value = null,
        bool $skipped = false
    ): void {
        $this->markTestSkipped('test not applicable');
    }
}
