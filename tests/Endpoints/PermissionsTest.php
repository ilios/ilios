<?php

namespace App\Tests\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use App\Tests\AbstractEndpointTest;

/**
 * Class PermissionsTest
 * @package App\Tests\Endpoints
 */
class PermissionsTest extends AbstractEndpointTest
{
    protected $testName = 'permissions';

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

        $response = self::$client->getResponse();
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

        $response = self::$client->getResponse();
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

        $response = self::$client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testGetOneFails()
    {
        $endpoint = $this->getPluralName();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('ilios_api_delete', ['version' => 'v1', 'object' => $endpoint, 'id' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = self::$client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testGetAllFails()
    {
        $endpoint = $this->getPluralName();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('ilios_api_getall', ['version' => 'v1', 'object' => $endpoint]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = self::$client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }
}
