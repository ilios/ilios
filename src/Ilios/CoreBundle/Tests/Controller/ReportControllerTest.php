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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadReportPoValueData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'title',
            'createdAt',
            'subject',
            'prepositionalObject',
            'deleted'
        ];
    }

    public function testGetReport()
    {
        $report = $this->container
            ->get('ilioscore.dataloader.report')
            ->getOne()
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
            $this->mockSerialize($report),
            json_decode($response->getContent(), true)['reports'][0]
        );
    }

    public function testGetAllReports()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_reports'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.report')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['reports']
        );
    }

    public function testPostReport()
    {
        $data = $this->container->get('ilioscore.dataloader.report')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_reports'),
            json_encode(['report' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['reports'][0],
            $response->getContent()
        );
    }

    public function testPostBadReport()
    {
        $invalidReport = $this->container
            ->get('ilioscore.dataloader.report')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_reports'),
            json_encode(['report' => $invalidReport])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutReport()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.report')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_reports',
                ['id' => $data['id']]
            ),
            json_encode(['report' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['report']
        );
    }

    public function testDeleteReport()
    {
        $report = $this->container
            ->get('ilioscore.dataloader.report')
            ->getOne()
        ;

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
            $this->getUrl('get_reports', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
