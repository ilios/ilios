<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * AlertChangeType controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AlertChangeTypeControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertChangeTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    /**
     * @group controllers_a
     */
    public function testGetAlertChangeType()
    {
        $alertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_alertchangetypes',
                ['id' => $alertChangeType['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($alertChangeType),
            json_decode($response->getContent(), true)['alertChangeTypes'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllAlertChangeTypes()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_alertchangetypes'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.alertchangetype')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['alertChangeTypes']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostAlertChangeType()
    {
        $data = $this->container->get('ilioscore.dataloader.alertchangetype')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alertchangetypes'),
            json_encode(['alertChangeType' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['alertChangeTypes'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostAlertAlertChangeType()
    {
        $data = $this->container->get('ilioscore.dataloader.alertchangetype')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alertchangetypes'),
            json_encode(['alertChangeType' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['alertChangeTypes'][0]['id'];
        foreach ($postData['alerts'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_alerts',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['alerts'][0];
            $this->assertTrue(in_array($newId, $data['changeTypes']));
        }
    }

    /**
     * @group controllers_a
     */
    public function testPostBadAlertChangeType()
    {
        $invalidAlertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alertchangetypes'),
            json_encode(['alertChangeType' => $invalidAlertChangeType]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutAlertChangeType()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_alertchangetypes',
                ['id' => $data['id']]
            ),
            json_encode(['alertChangeType' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['alertChangeType']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteAlertChangeType()
    {
        $alertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_alertchangetypes',
                ['id' => $alertChangeType['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_alertchangetypes',
                ['id' => $alertChangeType['id']]
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
    public function testAlertChangeTypeNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_alertchangetypes', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
