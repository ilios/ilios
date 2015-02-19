<?php

namespace Ilios\CoreBundle\Tests\Acceptance;

use Ilios\CoreBundle\Form\UserType;

class UsersTest extends ApiTestCase
{

    public function setup()
    {
        parent::setup();
        $this->loadFixtures(
            array(
                'Ilios\CoreBundle\Tests\Fixture\Users'
            )
        );
    }

    public function testJsonGetUserAction()
    {
        $users = $this->container->get('ilioscore.dataloader.users')->get();
        $user = array_shift($users);
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_user', array('id' => $user['id']))
        );


        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['user']['id']), $content);
        $this->assertEquals($user['id'], $decoded['user']['id'], $content);
        $this->assertEquals($user['email'], $decoded['user']['email'], $content);
    }

    public function testJsonGetNotFoundUserAction()
    {

        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_user', array('id' => -5))
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
    public function testJsonGetUsersAction()
    {
        $users = $this->container->get('ilioscore.dataloader.users')->get();
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_users')
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $content = $response->getContent();
        $decoded = json_decode($content, true);

        $this->assertTrue(isset($decoded['users']));
        $this->assertTrue(count($decoded['users']) == 20);
        for ($i = 0; $i < 20; $i++) {
            $user = array_shift($users);
            $this->assertEquals($user['id'], $decoded['users'][$i]['id']);
            $this->assertEquals($user['firstName'], $decoded['users'][$i]['firstName']);
        }
    }
    public function testJsonPostUserAction()
    {
        $faker = \Faker\Factory::create();

        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $email = $faker->email;
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_user'),
            $this->createJsonUser($firstName, $lastName, $email)
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $content = $response->getContent();
        $decoded = json_decode($content, true);

        $this->assertTrue(isset($decoded['user']));
        $this->assertEquals($firstName, $decoded['user']['firstName']);
        $this->assertEquals($lastName, $decoded['user']['lastName']);
        $this->assertEquals($email, $decoded['user']['email']);

    }
    public function testJsonPostUserActionWithBadParameters()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_user'),
            $this->createJsonUser('', '', 'notanemail')
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_user'),
            'baddatanouser'
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }
    public function test404BadRoute()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_user') . 'badroute'
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404, false);
    }
    public function testJsonPutUserActionShouldModify()
    {
        $users = $this->container->get('ilioscore.dataloader.users')->get();
        $user = array_shift($users);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl('put_user', array('id' => $user['id'])),
            $this->createJsonUser($user['firstName'], 'newname', $user['email'])
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['user']));
        $this->assertEquals($user['firstName'], $decoded['user']['firstName']);
        $this->assertEquals($user['email'], $decoded['user']['email']);
        $this->assertEquals('newname', $decoded['user']['lastName']);
    }
    public function testJsonPutUserActionShouldCreate()
    {
        $faker = \Faker\Factory::create();

        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $email = $faker->email;
        $this->createJsonRequest(
            'PUT',
            $this->getUrl('put_user', array('id' => 0)),
            $this->createJsonUser($firstName, $lastName, $email)
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201, true);
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['user']));
        $this->assertEquals($firstName, $decoded['user']['firstName']);
        $this->assertEquals($lastName, $decoded['user']['lastName']);
        $this->assertEquals($email, $decoded['user']['email']);
    }

    /**
     * Create a nicely formatted json user
     *
     * @param string $title
     * @return string
     */
    protected function createJsonUser($firstName, $lastName, $email)
    {
        $json = '{"' . 'user' . '":{' .
                '"firstName":"' . $firstName . '",' .
                '"lastName":"' . $lastName . '",' .
                '"email":"' . $email . '"' .
            '}}';
        return  $json;
    }
}
