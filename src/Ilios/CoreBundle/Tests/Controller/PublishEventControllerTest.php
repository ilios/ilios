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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData'
        ]);
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

    /**
     * @group controllers_b
     */
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($publishEvent),
            json_decode($response->getContent(), true)['publishEvents'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllPublishEvents()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_publishevents'),
            null,
            $this->getAuthenticatedUserToken()
        );
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

    /**
     * @group controllers_b
     */
    public function testPostPublishEvent()
    {
        $data = $this->container->get('ilioscore.dataloader.publishevent')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['programs']);
        unset($postData['programYears']);
        unset($postData['courses']);
        unset($postData['sessions']);
        unset($postData['offerings']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_publishevents'),
            json_encode(['publishEvent' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        // we're authenticating with user no. two (id = 2).
        // set it as the admin after posting the request.
        // TODO: this is super hinky. find a better way to deal with this. [ST 2015/08/13]
        $data['administrator'] = 2;

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['publishEvents'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostBadPublishEvent()
    {
        $invalidPublishEvent = $this->container
            ->get('ilioscore.dataloader.publishevent')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_publishevents'),
            json_encode(['publishEvent' => $invalidPublishEvent]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testPublishEventNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_publishevents', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
