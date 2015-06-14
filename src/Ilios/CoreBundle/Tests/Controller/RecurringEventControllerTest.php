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
     * @return array|string
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

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'onSunday',
            'onMonday',
            'onTuesday',
            'onWednesday',
            'onThursday',
            'onFriday',
            'onSaturday',
            'endDate',
            'repetitionCount'
        ];
    }

    public function testGetRecurringEvent()
    {
        $recurringEvent = $this->container
            ->get('ilioscore.dataloader.recurringevent')
            ->getOne()
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
            $this->mockSerialize($recurringEvent),
            json_decode($response->getContent(), true)['recurringEvents'][0]
        );
    }

    public function testGetAllRecurringEvents()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_recurringevents'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.recurringevent')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['recurringEvents']
        );
    }

    public function testPostRecurringEvent()
    {
        $data = $this->container->get('ilioscore.dataloader.recurringevent')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_recurringevents'),
            json_encode(['recurringEvent' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['recurringEvents'][0]
        );
    }

    public function testPostBadRecurringEvent()
    {
        $invalidRecurringEvent = $this->container
            ->get('ilioscore.dataloader.recurringevent')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_recurringevents'),
            json_encode(['recurringEvent' => $invalidRecurringEvent])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutRecurringEvent()
    {
        $recurringEvent = $this->container
            ->get('ilioscore.dataloader.recurringevent')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_recurringevents',
                ['id' => $recurringEvent['id']]
            ),
            json_encode(['recurringEvent' => $recurringEvent])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($recurringEvent),
            json_decode($response->getContent(), true)['recurringEvent']
        );
    }

    public function testDeleteRecurringEvent()
    {
        $recurringEvent = $this->container
            ->get('ilioscore.dataloader.recurringevent')
            ->getOne()
        ;

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
            $this->getUrl('get_recurringevents', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
