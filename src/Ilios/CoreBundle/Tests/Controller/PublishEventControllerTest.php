<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * PublishEvent controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class PublishEventControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'machineIp',
            'timeStamp',
            'tableName',
            'tableRowId'
        ];
    }

    public function testGetPublishEvent()
    {
        $publishEvent = $this->container
            ->get('ilioscore.dataloader.publishevent')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_publishevents',
                ['id' => $publishEvent['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($publishEvent),
            json_decode($response->getContent(), true)['publishEvents'][0]
        );
    }

    public function testGetAllPublishEvents()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_publishevents'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.publishevent')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['publishEvents']
        );
    }

    public function testPostPublishEvent()
    {
        $data = $this->container->get('ilioscore.dataloader.publishevent')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_publishevents'),
            json_encode(['publishEvent' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue(
            $response->headers->contains(
                'Location'
            ),
            print_r($response->headers, true)
        );
    }

    public function testPostBadPublishEvent()
    {
        $invalidPublishEvent = $this->container
            ->get('ilioscore.dataloader.publishevent')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_publishevents'),
            json_encode(['publishEvent' => $invalidPublishEvent])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutPublishEvent()
    {
        $publishEvent = $this->container
            ->get('ilioscore.dataloader.publishevent')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_publishevents',
                ['id' => $publishEvent['id']]
            ),
            json_encode(['publishEvent' => $publishEvent])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($publishEvent),
            json_decode($response->getContent(), true)['publishEvent']
        );
    }

    public function testDeletePublishEvent()
    {
        $publishEvent = $this->container
            ->get('ilioscore.dataloader.publishevent')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_publishevents',
                ['id' => $publishEvent['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_publishevents',
                ['id' => $publishEvent['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testPublishEventNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_publishevents', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
