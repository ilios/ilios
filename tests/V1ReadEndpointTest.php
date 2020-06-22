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
        $v3url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_${endpoint}_getall",
            ['version' => 'v3']
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
            $v3url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $v3Response = $this->kernelBrowser->getResponse();

        $v1Data = json_decode($v1Response->getContent(), true)[$responseKey];
        $v3Data = json_decode($v3Response->getContent(), true)[$responseKey];

        $this->assertNotEmpty($v1Data);
        $this->assertEquals(count($v3Data), count($v1Data));
        $v1Ids = array_column($v1Data, 'id');
        $v3Ids = array_column($v1Data, 'id');
        $this->assertEquals($v3Ids, $v1Ids);
    }
}
