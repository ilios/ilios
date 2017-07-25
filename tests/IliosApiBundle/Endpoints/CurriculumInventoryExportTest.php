<?php

namespace Tests\IliosApiBundle\Endpoints;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\IliosApiBundle\AbstractEndpointTest;
use DateTime;

/**
 * CurriculumInventoryExport API endpoint Test.
 * This is a POST only endpoint so that is all we will test
 * @group api_1
 */
class CurriculumInventoryExportTest extends AbstractEndpointTest
{
    protected $testName =  'curriculumInventoryExports';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryExportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryInstitutionData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceData',
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

        /** @var CurriculumInventoryReportInterface $report */
        $report = $this->fixtures->getReference('curriculumInventoryReports' . $data['report']);
        $export = $report->getExport();
        $this->assertNotEmpty($export);
        $this->assertGreaterThan(500, strlen($export->getDocument()));
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
        $parameters = array_merge(
            ['version' => 'v1', 'object' => 'curriculuminventoryexports'],
            $parameters
        );

        $url = $this->getUrl(
            'ilios_api_curriculuminventoryexport_404',
            $parameters
        );
        $this->createJsonRequest(
            $type,
            $url,
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }
}
