<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * User controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class UserControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'phone',
            'addedViaIlios',
            'enabled',
            'ucUid',
            'otherId',
            'examined',
            'userSyncIgnore',
            'apiKey',
            'reminders'
        ];
    }

    public function testGetUser()
    {
        $user = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_users',
                ['id' => $user['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($user),
            json_decode($response->getContent(), true)['users'][0]
        );
    }

    public function testGetAllUsers()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_users'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.user')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['users']
        );
    }

    public function testFindUsers()
    {
        $users = $this->container->get('ilioscore.dataloader.user')->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'first'))
        );

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(1, count($gotUsers));
        $this->assertEquals(
            $users[1]['id'],
            $gotUsers[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'second'))
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(1, count($gotUsers));
        $this->assertEquals(
            $users[2]['id'],
            $gotUsers[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'example'))
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(2, count($gotUsers));
        $this->assertEquals(
            $users[1]['id'],
            $gotUsers[0]['id']
        );
        $this->assertEquals(
            $users[2]['id'],
            $gotUsers[1]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'example second'))
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(1, count($gotUsers));
        $this->assertEquals(
            $users[2]['id'],
            $gotUsers[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'nobodyxyzmartian'))
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(0, count($gotUsers));
    }

    public function testPostUser()
    {
        $data = $this->container->get('ilioscore.dataloader.user')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['instructedOfferings']);
        unset($postData['cohorts']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];
        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['users'][0],
            $response->getContent()
        );
    }

    public function testPostBadUser()
    {
        $invalidUser = $this->container
            ->get('ilioscore.dataloader.user')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $invalidUser])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutUser()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['instructedOfferings']);
        unset($postData['cohorts']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_users',
                ['id' => $data['id']]
            ),
            json_encode(['user' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['user']
        );
    }

    public function testDeleteUser()
    {
        $user = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_users',
                ['id' => $user['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_users',
                ['id' => $user['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testUserNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_users', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
