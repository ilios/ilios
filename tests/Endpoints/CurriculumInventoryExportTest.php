<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadApplicationConfigData;
use App\Tests\Fixture\LoadCurriculumInventoryInstitutionData;
use App\Tests\Fixture\LoadCurriculumInventoryExportData;
use App\Tests\Fixture\LoadCurriculumInventoryReportData;
use App\Tests\Fixture\LoadCurriculumInventorySequenceData;
use App\Tests\Fixture\LoadUserData;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

/**
 * CurriculumInventoryExport API endpoint Test.
 * This is a POST only endpoint so that is all we will test
 */
#[\PHPUnit\Framework\Attributes\Group('api_1')]
class CurriculumInventoryExportTest extends AbstractEndpoint
{
    protected string $testName =  'curriculumInventoryExports';

    protected function getFixtures(): array
    {
        return [
            LoadUserData::class,
            LoadApplicationConfigData::class,
            LoadCurriculumInventoryReportData::class,
            LoadCurriculumInventoryExportData::class,
            LoadCurriculumInventoryInstitutionData::class,
            LoadCurriculumInventorySequenceData::class,
        ];
    }

    public function testPostCurriculumInventoryExport(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();

        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $postKey = $this->getCamelCasedSingularName();
        $responseData = $this->postOne($endpoint, $postKey, $responseKey, $data, $jwt);

        $this->assertEquals($responseData['report'], $data['report']);
        $this->assertNotEmpty($responseData['createdBy']);
        $this->assertNotEmpty($responseData['createdAt']);

        $now = new DateTime();
        $stamp = new DateTime($responseData['createdAt']);
        $diff = $now->diff($stamp);
        $this->assertTrue($diff->days < 2, "The createdAt timestamp is within the last day");
        $this->assertArrayNotHasKey('document', $responseData, 'Document is not part of payload.');
    }

    /**
     * Test posting a single object
     */
    public function testPostOneJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $jsonApiData = $dataLoader->createJsonApi($data);
        $responseData = $this->postOneJsonApi($jsonApiData, $jwt);

        $this->assertEquals((int) $responseData->relationships->report->data->id, $data['report']);
        $this->assertNotEmpty($responseData->attributes->createdBy);
        $this->assertNotEmpty($responseData->attributes->createdAt);

        $now = new DateTime();
        $stamp = new DateTime($responseData->attributes->createdAt);
        $diff = $now->diff($stamp);
        $this->assertTrue($diff->days < 2, "The createdAt timestamp is within the last day");
        $this->assertFalse(
            property_exists($responseData->attributes, 'document'),
            'Document is not part of payload.'
        );
    }

    public function testAnonymousPostDenied(): void
    {
        $url = '/api/' . $this->apiVersion . '/curriculuminventoryexports/';
        $this->createJsonRequest(
            'POST',
            $url,
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function testGetIs404(): void
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $id = $data['id'];

        $this->fourOhFourTest('GET', ['id' => $id]);
    }

    public function testGetAllIs404(): void
    {
        $this->fourOhFourTest('GET');
    }

    public function testPutIs404(): void
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $id = $data['id'];

        $this->fourOhFourTest('PUT', ['id' => $id]);
    }

    public function testPatchIs404(): void
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $id = $data['id'];

        $this->fourOhFourTest('PATCH', ['id' => $id]);
    }

    public function testDeleteIs404(): void
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $id = $data['id'];

        $this->fourOhFourTest('DELETE', ['id' => $id]);
    }

    public function testAccessDeniedWithServiceToken(): void
    {
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools(
            $this->kernelBrowser,
            $this->fixtures
        );
        $data = $this->getDataLoader()->create();
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_curriculuminventoryexports_post',
                ['version' => $this->apiVersion],
            ),
            json_encode($data)
        );
        $this->canNotJsonApi(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_curriculuminventoryexports_post',
                ['version' => $this->apiVersion],
            ),
            json_encode($data)
        );
    }

    protected function fourOhFourTest(string $type, array $parameters = []): void
    {
        $url = '/api/' . $this->apiVersion . '/curriculuminventoryexports/';
        if (array_key_exists('id', $parameters)) {
            $url .= $parameters['id'];
        }
        $this->createJsonRequest(
            $type,
            $url,
            null,
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }
}
