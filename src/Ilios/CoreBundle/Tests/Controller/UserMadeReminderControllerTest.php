<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

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
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserMadeReminderData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'note',
            'createdAt',
            'dueDate',
            'closed'
        ];
    }

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
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($userMadeReminder),
            json_decode($response->getContent(), true)['userMadeReminders'][0]
        );
    }

    public function testGetAllUserMadeReminders()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_usermadereminders'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.usermadereminder')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['userMadeReminders']
        );
    }

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
            json_encode(['userMadeReminder' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['userMadeReminders'][0],
            $response->getContent()
        );
    }

    public function testPostBadUserMadeReminder()
    {
        $invalidUserMadeReminder = $this->container
            ->get('ilioscore.dataloader.usermadereminder')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_usermadereminders'),
            json_encode(['userMadeReminder' => $invalidUserMadeReminder])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

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
            json_encode(['userMadeReminder' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['userMadeReminder']
        );
    }

    public function testDeleteUserMadeReminder()
    {
        $userMadeReminder = $this->container
            ->get('ilioscore.dataloader.usermadereminder')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_usermadereminders',
                ['id' => $userMadeReminder['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_usermadereminders',
                ['id' => $userMadeReminder['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testUserMadeReminderNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_usermadereminders', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
