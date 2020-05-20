<?php

declare(strict_types=1);

namespace App\Tests;

/**
 * Class V1ReadEndpointTest
 * @package App\Tests
 */
abstract class V1ReadEndpointTest extends ReadEndpointTest
{

    protected $apiVersion = 'v1';

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function testGetAll()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_${endpoint}_getall",
            ['version' => $this->apiVersion]
        );
        $v2url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_${endpoint}_getall",
            ['version' => 'v2']
        );
        $this->createJsonRequest(
            'GET',
            $v1url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $v1Response = $this->kernelBrowser->getResponse();

        $this->createJsonRequest(
            'GET',
            $v2url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $v2Response = $this->kernelBrowser->getResponse();

        $v1Data = json_decode($v1Response->getContent(), true)[$responseKey];
        $v2Data = json_decode($v2Response->getContent(), true)[$responseKey];

        $this->assertNotEmpty($v1Data);
        $this->assertEquals(count($v2Data), count($v1Data));
        $v1Ids = array_column($v1Data, 'id');
        $v2Ids = array_column($v1Data, 'id');
        $this->assertEquals($v2Ids, $v1Ids);
    }
}
