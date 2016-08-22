<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
    }

    /**
     * @group controllers_b
     */
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['reports'][0];
        $createdAt = new DateTime($data['createdAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($report),
            $data
        );
        $now = new DateTime();
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->i < 10, 'The createdAt timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers_b
     */
    public function testGetAllReports()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_reports'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = [];
        $responses = json_decode($response->getContent(), true)['reports'];
        $now = new DateTime();
        foreach ($responses as $response) {
            $createdAt = new DateTime($response['createdAt']);
            unset($response['createdAt']);
            $diff = $now->diff($createdAt);
            $this->assertTrue($diff->i < 10, 'The createdAt timestamp is within the last 10 minutes');
            $data[] = $response;
        }
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.report')
                    ->getAll()
            ),
            $data
        );
    }

    /**
     * @group controllers_b
     */
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
            json_encode(['report' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)['reports'][0];
        $createdAt = new DateTime($responseData['createdAt']);
        unset($responseData['createdAt']);
        $this->assertEquals(
            $data,
            $responseData,
            $response->getContent()
        );
        $now = new DateTime();
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->i < 10, 'The createdAt timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers_b
     */
    public function testPostBadReport()
    {
        $invalidReport = $this->container
            ->get('ilioscore.dataloader.report')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_reports'),
            json_encode(['report' => $invalidReport]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
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
            json_encode(['report' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['report'];
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($data),
            $data
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteReport()
    {
        $report = $this->container
            ->get('ilioscore.dataloader.report')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_reports',
                ['id' => $report['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_reports',
                ['id' => $report['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testReportNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_reports', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
