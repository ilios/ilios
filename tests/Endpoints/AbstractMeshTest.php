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
                'ilios_api_post',
                ['version' => $this->apiVersion, 'object' => $endpoint]
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
                'ilios_api_put',
                ['version' => $this->apiVersion, 'object' => $endpoint, 'id' => 1]
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
                'ilios_api_delete',
                ['version' => $this->apiVersion, 'object' => $endpoint, 'id' => 1]
            ),
            json_encode([$responseKey => []]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }
}
