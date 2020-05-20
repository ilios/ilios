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
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_post",
                ['version' => $this->apiVersion]
            ),
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testPutFails()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedSingularName();

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_put",
                ['version' => $this->apiVersion, 'id' => 1]
            ),
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testDeleteFails()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedSingularName();

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_delete",
                ['version' => $this->apiVersion, 'id' => 1]
            ),
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }
}
