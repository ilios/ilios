<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * AamcMethod API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class AcademicYearTest extends AbstractEndpointTest
{
    protected $testName =  'academicyears';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCourseData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
//            'id' => [[1], ['id' => 2013]],
//            'ids' => [[0, 2], ['id' => [2012, 2016]]],
//            'title' => [[1], ['id' => 2013]],
//            'titles' => [[0, 2], ['id' => [2012, 2016]]],
        ];
    }


    public function testGetOne()
    {
        $pluralObjectName = $this->getPluralName();
        $academicYears = $this->getYears();
        $data = $academicYears[0];
        $returnedData = $this->getOne($pluralObjectName, $data['id']);
        $this->compareData($data, $returnedData);
    }

    public function testGetAll()
    {
        $pluralObjectName = $this->getPluralName();
        $academicYears = $this->getYears();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_getall',
                ['version' => 'v1', 'object' => $pluralObjectName]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responses = json_decode($response->getContent(), true)[$pluralObjectName];


        $this->assertEquals(
            $academicYears,
            $responses
        );
    }

    /**
     * @dataProvider filtersToTest
     */
    public function testFilters(array $dataKeys = [], array $filterParts = [])
    {
        if (empty($filterParts)) {
            $this->markTestSkipped('Missing filters tests for this endpoint');
            return;
        }
        $all = $this->getYears();
        $expectedData = array_map(function ($i) use ($all) {
            return $all[$i];
        }, $dataKeys);
        $filters = [];
        foreach ($filterParts as $key => $value) {
            $filters["filters[{$key}]"] = $value;
        }
        $this->filterTest($filters, $expectedData);
    }

    public function testPostIs404()
    {
        $this->fourOhFourTest('POST');
    }

    public function testPutIs404()
    {
        $academicYears = $this->getYears();
        $id = $academicYears[0]['id'];

        $this->fourOhFourTest('PUT', ['id' => $id]);
    }

    public function testDeleteIs404()
    {
        $academicYears = $this->getYears();
        $id = $academicYears[0]['id'];

        $this->fourOhFourTest('DELETE', ['id' => $id]);
    }

    protected function fourOhFourTest($type, array $parameters = [])
    {
        $parameters = array_merge(
            ['version' => 'v1', 'object' => 'academicyears'],
            $parameters
        );

        $url = $this->getUrl(
            'ilios_api_academicyear_404',
            $parameters
        );
        $this->createJsonRequest(
            $type,
            $url,
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    protected function getYears()
    {
        $loader = $this->container->get('ilioscore.dataloader.course');
        $data = $loader->getAll();
        $academicYears = array_map(function ($arr) {
            return $arr['year'];
        }, $data);
        $academicYears = array_unique($academicYears);
        sort($academicYears);
        $academicYears = array_map(function ($year) {
            return [
                'id' => $year,
                'title' => $year
            ];
        }, $academicYears);

        return $academicYears;
    }
}
