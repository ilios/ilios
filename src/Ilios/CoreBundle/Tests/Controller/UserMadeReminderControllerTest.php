<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use \DateTime;

/**
 * UserMadeReminder controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class UserMadeReminderControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserMadeReminderData',
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
    public function testGetUserMadeReminder()
    {
        $userMadeReminder = $this->container
            ->get('ilioscore.dataloader.usermadereminder')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_usermadereminders',
                ['id' => $userMadeReminder['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['userMadeReminders'][0];
        $createdAt = new DateTime($data['createdAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($userMadeReminder),
            $data
        );
        $now = new DateTime();
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->i < 10, 'The createdAt timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers_b
     */
    public function testGetAllUserMadeReminders()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_usermadereminders'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = [];
        $responses = json_decode($response->getContent(), true)['userMadeReminders'];
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
                    ->get('ilioscore.dataloader.usermadereminder')
                    ->getAll()
            ),
            $data
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostUserMadeReminder()
    {
        $data = $this->container->get('ilioscore.dataloader.usermadereminder')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_usermadereminders'),
            json_encode(['userMadeReminder' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)['userMadeReminders'][0];
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
    public function testPostBadUserMadeReminder()
    {
        $invalidUserMadeReminder = $this->container
            ->get('ilioscore.dataloader.usermadereminder')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_usermadereminders'),
            json_encode(['userMadeReminder' => $invalidUserMadeReminder]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testPutUserMadeReminder()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.usermadereminder')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_usermadereminders',
                ['id' => $data['id']]
            ),
            json_encode(['userMadeReminder' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responseData = json_decode($response->getContent(), true)['userMadeReminder'];
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
     * @group controllers
     */
    public function testDeleteUserMadeReminder()
    {
        $userMadeReminder = $this->container
            ->get('ilioscore.dataloader.usermadereminder')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_usermadereminders',
                ['id' => $userMadeReminder['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_usermadereminders',
                ['id' => $userMadeReminder['id']]
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
    public function testUserMadeReminderNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_usermadereminders', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
