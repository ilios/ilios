<?php

declare(strict_types=1);

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

    public function testGetOneFails()
    {
        $endpoint = $this->getPluralName();

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_delete",
                ['version' => $this->apiVersion, 'id' => 1]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testGetAllFails()
    {
        $endpoint = $this->getPluralName();

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }
}
