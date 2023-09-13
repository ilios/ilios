<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadIngestionExceptionData;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\AbstractReadEndpoint;

/**
 * IngestionException API endpoint Test.
 * @group api_4
 */
class IngestionExceptionTest extends AbstractReadEndpoint
{
    protected string $testName =  'ingestionExceptions';
    protected bool $isGraphQLTestable = false;

    protected function getFixtures(): array
    {
        return [
            LoadIngestionExceptionData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
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
        $url = '/api/' . $this->apiVersion . '/ingestionexceptions/';
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
