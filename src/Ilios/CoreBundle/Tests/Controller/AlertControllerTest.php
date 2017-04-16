<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Alert controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AlertControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertChangeTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData'
        ];
    }

    public function testGetAlert()
    {
        $alert = $this->container
            ->get('ilioscore.dataloader.alert')
            ->getOne()['alert']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_alerts',
                ['id' => $alert['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $alert,
            json_decode($response->getContent(), true)['alert']
        );
    }

    public function testGetAllAlerts()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_alerts'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.alert')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostAlert()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alerts'),
            json_encode(
                $this->container->get('ilioscore.dataloader.alert')
                    ->create()['alert']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadAlert()
    {
        $invalidAlert = array_shift(
            $this->container->get('ilioscore.dataloader.alert')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alerts'),
            $invalidAlert
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutAlert()
    {
        $alert = $this->container
            ->get('ilioscore.dataloader.alert')
            ->createWithId()['alert']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_alerts',
                ['id' => $alert['id']]
            ),
            json_encode($alert)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.alert')
                ->getLastCreated()['alert'],
            json_decode($response->getContent(), true)['alert']
        );
    }

    public function testDeleteAlert()
    {
        $alert = $this->container
            ->get('ilioscore.dataloader.alert')
            ->createWithId()['alert']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_alerts',
                ['id' => $alert['id']]
            ),
            json_encode($alert)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_alerts',
                ['id' => $alert['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_alerts',
                ['id' => $alert['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testAlertNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_alerts', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
