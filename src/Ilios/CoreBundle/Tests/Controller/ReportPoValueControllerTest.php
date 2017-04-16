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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadReportPoValueData',
            'Ilios\CoreBundle\Tests\Fixture\LoadReportData'
        ];
    }

    public function testGetReportPoValue()
    {
        $reportPoValue = $this->container
            ->get('ilioscore.dataloader.reportpovalue')
            ->getOne()['reportPoValue']
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
            $reportPoValue,
            json_decode($response->getContent(), true)['reportPoValue']
        );
    }

    public function testGetAllReportPoValues()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_reportpovalues'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.reportpovalue')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostReportPoValue()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_reportpovalues'),
            json_encode(
                $this->container->get('ilioscore.dataloader.reportpovalue')
                    ->create()['reportPoValue']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadReportPoValue()
    {
        $invalidReportPoValue = array_shift(
            $this->container->get('ilioscore.dataloader.reportpovalue')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_reportpovalues'),
            $invalidReportPoValue
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutReportPoValue()
    {
        $reportPoValue = $this->container
            ->get('ilioscore.dataloader.reportpovalue')
            ->createWithId()['reportPoValue']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_reportpovalues',
                ['id' => $reportPoValue['report']]
            ),
            json_encode($reportPoValue)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.reportpovalue')
                ->getLastCreated()['reportPoValue'],
            json_decode($response->getContent(), true)['reportPoValue']
        );
    }

    public function testDeleteReportPoValue()
    {
        $reportPoValue = $this->container
            ->get('ilioscore.dataloader.reportpovalue')
            ->createWithId()['reportPoValue']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_reportpovalues',
                ['id' => $reportPoValue['report']]
            ),
            json_encode($reportPoValue)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_reportpovalues', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
