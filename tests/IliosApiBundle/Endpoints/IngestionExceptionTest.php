<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * IngestionException API endpoint Test.
 * @group api_4
 */
class IngestionExceptionTest extends AbstractEndpointTest
{
    protected $testName =  'ingestionExceptions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadIngestionExceptionData',
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
            'uid' => [[1], ['uid' => 'second exception']],
            'user' => [[1], ['user' => 2]],
        ];
    }

    public function testGetOne()
    {
        $this->getOneTest();
    }

    public function testGetAll()
    {
        $this->getAllTest();
    }

    /**
     * @inheritdoc
     * @dataProvider filtersToTest
     */
    public function testFilters(array $dataKeys = [], array $filterParts = [])
    {
        if (empty($filterParts)) {
            $this->markTestSkipped('Missing filters tests for this endpoint');
            return;
        }
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
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
            ['version' => 'v1', 'object' => 'ingestionexceptions'],
            $parameters
        );

        $url = $this->getUrl(
            'ilios_api_ingestionexception_404',
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
