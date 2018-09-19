<?php

namespace App\Tests\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use App\Tests\ReadEndpointTest;

/**
 * IngestionException API endpoint Test.
 * @group api_4
 */
class IngestionExceptionTest extends ReadEndpointTest
{
    protected $testName =  'ingestionExceptions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadIngestionExceptionData',
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
