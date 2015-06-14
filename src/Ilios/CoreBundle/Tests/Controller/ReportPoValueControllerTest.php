<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * ReportPoValue controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class ReportPoValueControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadReportPoValueData',
            'Ilios\CoreBundle\Tests\Fixture\LoadReportData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'prepositionalObjectTableRowId',
            'deleted'
        ];
    }

    public function testGetReportPoValue()
    {
        $reportPoValue = $this->container
            ->get('ilioscore.dataloader.reportpovalue')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_reportpovalues',
                ['id' => $reportPoValue['report']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($reportPoValue),
            json_decode($response->getContent(), true)['reportPoValues'][0]
        );
    }

    public function testGetAllReportPoValues()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_reportpovalues'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.reportpovalue')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['reportPoValues']
        );
    }

    public function testPostReportPoValue()
    {
        $data = $this->container->get('ilioscore.dataloader.reportpovalue')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_reportpovalues'),
            json_encode(['reportPoValue' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['reportPoValues'][0]
        );
    }

    public function testPostBadReportPoValue()
    {
        $invalidReportPoValue = $this->container
            ->get('ilioscore.dataloader.reportpovalue')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_reportpovalues'),
            json_encode(['reportPoValue' => $invalidReportPoValue])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutReportPoValue()
    {
        $reportPoValue = $this->container
            ->get('ilioscore.dataloader.reportpovalue')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_reportpovalues',
                ['id' => $reportPoValue['report']]
            ),
            json_encode(['reportPoValue' => $reportPoValue])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($reportPoValue),
            json_decode($response->getContent(), true)['reportPoValue']
        );
    }

    public function testDeleteReportPoValue()
    {
        $reportPoValue = $this->container
            ->get('ilioscore.dataloader.reportpovalue')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_reportpovalues',
                ['id' => $reportPoValue['report']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_reportpovalues',
                ['id' => $reportPoValue['report']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testReportPoValueNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_reportpovalues', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
