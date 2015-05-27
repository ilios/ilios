<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Report controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class ReportControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadReportPoValueData'
        ];
    }

    public function testGetReport()
    {
        $report = $this->container
            ->get('ilioscore.dataloader.report')
            ->getOne()['report']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_reports',
                ['id' => $report['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $report,
            json_decode($response->getContent(), true)['report']
        );
    }

    public function testGetAllReports()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_reports'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.report')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostReport()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_reports'),
            json_encode(
                $this->container->get('ilioscore.dataloader.report')
                    ->create()['report']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadReport()
    {
        $invalidReport = array_shift(
            $this->container->get('ilioscore.dataloader.report')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_reports'),
            $invalidReport
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutReport()
    {
        $report = $this->container
            ->get('ilioscore.dataloader.report')
            ->createWithId()['report']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_reports',
                ['id' => $report['id']]
            ),
            json_encode($report)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.report')
                ->getLastCreated()['report'],
            json_decode($response->getContent(), true)['report']
        );
    }

    public function testDeleteReport()
    {
        $report = $this->container
            ->get('ilioscore.dataloader.report')
            ->createWithId()['report']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_reports',
                ['id' => $report['id']]
            ),
            json_encode($report)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_reports',
                ['id' => $report['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_reports',
                ['id' => $report['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testReportNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_reports', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
