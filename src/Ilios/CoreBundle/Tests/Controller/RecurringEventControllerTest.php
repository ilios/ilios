<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * RecurringEvent controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class RecurringEventControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadRecurringEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadRecurringEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadRecurringEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData'
        ];
    }

    public function testGetRecurringEvent()
    {
        $recurringEvent = $this->container
            ->get('ilioscore.dataloader.recurringevent')
            ->getOne()['recurringEvent']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_recurringevents',
                ['id' => $recurringEvent['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $recurringEvent,
            json_decode($response->getContent(), true)['recurringEvent']
        );
    }

    public function testGetAllRecurringEvents()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_recurringevents'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.recurringevent')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostRecurringEvent()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_recurringevents'),
            json_encode(
                $this->container->get('ilioscore.dataloader.recurringevent')
                    ->create()['recurringEvent']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadRecurringEvent()
    {
        $invalidRecurringEvent = array_shift(
            $this->container->get('ilioscore.dataloader.recurringevent')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_recurringevents'),
            $invalidRecurringEvent
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutRecurringEvent()
    {
        $recurringEvent = $this->container
            ->get('ilioscore.dataloader.recurringevent')
            ->createWithId()['recurringEvent']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_recurringevents',
                ['id' => $recurringEvent['id']]
            ),
            json_encode($recurringEvent)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.recurringevent')
                ->getLastCreated()['recurringEvent'],
            json_decode($response->getContent(), true)['recurringEvent']
        );
    }

    public function testDeleteRecurringEvent()
    {
        $recurringEvent = $this->container
            ->get('ilioscore.dataloader.recurringevent')
            ->createWithId()['recurringEvent']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_recurringevents',
                ['id' => $recurringEvent['id']]
            ),
            json_encode($recurringEvent)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_recurringevents',
                ['id' => $recurringEvent['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_recurringevents',
                ['id' => $recurringEvent['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testRecurringEventNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_recurringevents', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
