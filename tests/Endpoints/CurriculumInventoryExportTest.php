<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\DTO\CurriculumInventoryExportDTO;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\AbstractEndpointTest;
use DateTime;

/**
 * CurriculumInventoryExport API endpoint Test.
 * This is a POST only endpoint so that is all we will test
 * @group api_1
 */
class CurriculumInventoryExportTest extends AbstractEndpointTest
{
    protected string $testName =  'curriculumInventoryExports';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadApplicationConfigData',
            'App\Tests\Fixture\LoadCurriculumInventoryReportData',
            'App\Tests\Fixture\LoadCurriculumInventoryExportData',
            'App\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'App\Tests\Fixture\LoadCurriculumInventorySequenceData',
        ];
    }

    public function testPostCurriculumInventoryExport()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();

        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $postKey = $this->getCamelCasedSingularName();
        $responseData = $this->postOne($endpoint, $postKey, $responseKey, $data);

        $this->assertEquals($responseData['report'], $data['report']);
        $this->assertNotEmpty($responseData['createdBy']);
        $this->assertNotEmpty($responseData['createdAt']);

        $now = new DateTime();
        $stamp = new DateTime($responseData['createdAt']);
        $diff = $now->diff($stamp);
        $this->assertTrue($diff->days < 2, "The createdAt timestamp is within the last day");
        $this->assertFalse(array_key_exists('document', $responseData), 'Document is not part of payload.');
    }

    /**
     * Test posting a single object
     */
    public function testPostOneJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $jsonApiData = $dataLoader->createJsonApi($data);
        $responseData = $this->postOneJsonApi($jsonApiData);

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

    public function testGetIs404()
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $id = $data['id'];

        $this->fourOhFourTest('GET', ['id' => $id]);
    }

    public function testGetAllIs404()
    {
        $this->fourOhFourTest('GET');
    }

    public function testPutIs404()
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $id = $data['id'];

        $this->fourOhFourTest('PUT', ['id' => $id]);
    }

    public function testDeleteIs404()
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $id = $data['id'];

        $this->fourOhFourTest('DELETE', ['id' => $id]);
    }

    protected function fourOhFourTest($type, array $parameters = [])
    {
        $url = '/api/' . $this->apiVersion . '/curriculuminventoryexports/';
        if (array_key_exists('id', $parameters)) {
            $url .= $parameters['id'];
        }
        $this->createJsonRequest(
            $type,
            $url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }
}
