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
     * @return array
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

    public function testGetPublishEvent()
    {
        $publishEvent = $this->container
            ->get('ilioscore.dataloader.publishevent')
            ->getOne()['publishEvent']
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
            $publishEvent,
            json_decode($response->getContent(), true)['publishEvent']
        );
    }

    public function testGetAllPublishEvents()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_publishevents'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.publishevent')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostPublishEvent()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_publishevents'),
            json_encode(
                $this->container->get('ilioscore.dataloader.publishevent')
                    ->create()['publishEvent']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadPublishEvent()
    {
        $invalidPublishEvent = array_shift(
            $this->container->get('ilioscore.dataloader.publishevent')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_publishevents'),
            $invalidPublishEvent
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutPublishEvent()
    {
        $publishEvent = $this->container
            ->get('ilioscore.dataloader.publishevent')
            ->createWithId()['publishEvent']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_publishevents',
                ['id' => $publishEvent['id']]
            ),
            json_encode($publishEvent)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.publishevent')
                ->getLastCreated()['publishEvent'],
            json_decode($response->getContent(), true)['publishEvent']
        );
    }

    public function testDeletePublishEvent()
    {
        $publishEvent = $this->container
            ->get('ilioscore.dataloader.publishevent')
            ->createWithId()['publishEvent']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_publishevents',
                ['id' => $publishEvent['id']]
            ),
            json_encode($publishEvent)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_publishevents', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
