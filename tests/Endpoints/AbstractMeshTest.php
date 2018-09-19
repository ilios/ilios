<?php

namespace Tests\App\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\App\ReadEndpointTest;

/**
 * Class AbstractMeshTest
 * @package Tests\AppBundle\Endpoints
 */
abstract class AbstractMeshTest extends ReadEndpointTest
{
    public function testPostFails()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedSingularName();

        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $endpoint]),
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testPutFails()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedSingularName();

        $this->createJsonRequest(
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => $endpoint, 'id' => 1]),
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testDeleteFails()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedSingularName();

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl('ilios_api_delete', ['version' => 'v1', 'object' => $endpoint, 'id' => 1]),
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }
}
