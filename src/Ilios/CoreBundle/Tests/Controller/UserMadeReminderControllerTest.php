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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserMadeReminderData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ];
    }

    public function testGetUserMadeReminder()
    {
        $userMadeReminder = $this->container
            ->get('ilioscore.dataloader.usermadereminder')
            ->getOne()['userMadeReminder']
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
            $userMadeReminder,
            json_decode($response->getContent(), true)['userMadeReminder']
        );
    }

    public function testGetAllUserMadeReminders()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_usermadereminders'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.usermadereminder')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostUserMadeReminder()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_usermadereminders'),
            json_encode(
                $this->container->get('ilioscore.dataloader.usermadereminder')
                    ->create()['userMadeReminder']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadUserMadeReminder()
    {
        $invalidUserMadeReminder = array_shift(
            $this->container->get('ilioscore.dataloader.usermadereminder')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_usermadereminders'),
            $invalidUserMadeReminder
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutUserMadeReminder()
    {
        $userMadeReminder = $this->container
            ->get('ilioscore.dataloader.usermadereminder')
            ->createWithId()['userMadeReminder']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_usermadereminders',
                ['id' => $userMadeReminder['id']]
            ),
            json_encode($userMadeReminder)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.usermadereminder')
                ->getLastCreated()['userMadeReminder'],
            json_decode($response->getContent(), true)['userMadeReminder']
        );
    }

    public function testDeleteUserMadeReminder()
    {
        $userMadeReminder = $this->container
            ->get('ilioscore.dataloader.usermadereminder')
            ->createWithId()['userMadeReminder']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_usermadereminders',
                ['id' => $userMadeReminder['id']]
            ),
            json_encode($userMadeReminder)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_usermadereminders', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
