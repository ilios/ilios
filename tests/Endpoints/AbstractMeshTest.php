<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use App\Tests\ReadEndpointTest;

/**
 * Class AbstractMeshTest
 * @package App\Tests\Endpoints
 */
abstract class AbstractMeshTest extends ReadEndpointTest
{
    public function testPostFails()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedSingularName();

        $this->createJsonRequest(
            'POST',
            '/api/' . $this->apiVersion . "/{$endpoint}/",
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testPutFails()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedSingularName();

        $this->createJsonRequest(
            'PUT',
            '/api/' . $this->apiVersion . "/{$endpoint}/1",
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testDeleteFails()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedSingularName();

        $this->createJsonRequest(
            'DELETE',
            '/api/' . $this->apiVersion . "/{$endpoint}/1",
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }
}
