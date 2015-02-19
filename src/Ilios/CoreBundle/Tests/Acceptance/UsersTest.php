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
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_user'),
            $this->createJsonUser('Some good data')
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
    }
    // public function testJsonPostUserActionWithBadParameters()
    // {
    //     $badTitles = array(
    //         '',
    //         'a',
    //         str_pad('a', 256, 'a')
    //     );
    //     foreach ($badTitles as $title) {
    //         $client = $this->createJsonRequest(
    //             'POST',
    //             $this->getUrl('post_user'),
    //             $this->createJsonUser($title)
    //         );
    //         $this->assertJsonResponse($client->getResponse(), 400);
    //     }
    //
    //     $client = $this->createJsonRequest(
    //         'POST',
    //         $this->getUrl('post_user'),
    //         'baddatanotitle'
    //     );
    //     $this->assertJsonResponse($client->getResponse(), 400);
    // }
    // public function test404BadRoute()
    // {
    //     $client = $this->createJsonRequest(
    //         'POST',
    //         $this->getUrl('post_user') . 'badroute'
    //     );
    //     $this->assertJsonResponse($client->getResponse(), 404);
    // }
    // public function testJsonPutUserActionShouldModify()
    // {
    //
    //
    //     $client = $this->createJsonRequest(
    //         'PUT',
    //         $this->getUrl('put_user', array('id' => 1)),
    //         $this->createJsonUser('ChangedTitle')
    //     );
    //     $this->assertJsonResponse($client->getResponse(), 202);
    //     $content = $client->getResponse()->getContent();
    //     $decoded = json_decode($content, true);
    //     $this->assertTrue(isset($decoded['user']));
    //     $this->assertEquals('ChangedTitle', $decoded['user']['title']);
    // }
    // public function testJsonPutUserActionShouldCreate()
    // {
    //
    //     $client = static::createClient();
    //     $client = $this->createJsonRequest(
    //         'PUT',
    //         $this->getUrl('put_user', array('id' => 0)),
    //         $this->createJsonUser('newtitle')
    //     );
    //     $this->assertJsonResponse($client->getResponse(), 201, true);
    //     $content = $client->getResponse()->getContent();
    //     $decoded = json_decode($content, true);
    //     $this->assertTrue(isset($decoded['user']));
    //     $this->assertEquals('newtitle', $decoded['user']['title']);
    // }
    // public function testJsonPutExistingUserBadData()
    // {
    //
    //     $badTitles = array(
    //         '',
    //         'a',
    //         str_pad('a', 256, 'a')
    //     );
    //     foreach ($badTitles as $title) {
    //         $client = $this->createJsonRequest(
    //             'PUT',
    //             $this->getUrl('put_user', array('id' => 1)),
    //             $this->createJsonUser($title)
    //         );
    //         $this->assertJsonResponse($client->getResponse(), 400);
    //     }
    //
    //     $client = $this->createJsonRequest(
    //         'PUT',
    //         $this->getUrl('put_user', array('id' => 1)),
    //         'baddatanotitle'
    //     );
    //     $this->assertJsonResponse($client->getResponse(), 400);
    // }
    // public function testJsonPutNewUserBadData()
    // {
    //     $badTitles = array(
    //         '',
    //         'a',
    //         str_pad('a', 256, 'a')
    //     );
    //     foreach ($badTitles as $title) {
    //         $client = $this->createJsonRequest(
    //             'PUT',
    //             $this->getUrl('put_user', array('id' => 0)),
    //             $this->createJsonUser($title)
    //         );
    //         $this->assertJsonResponse($client->getResponse(), 400);
    //     }
    //
    //     $client = $this->createJsonRequest(
    //         'PUT',
    //         $this->getUrl('put_user', array('id' => 0)),
    //         'baddatanotitle'
    //     );
    //     $this->assertJsonResponse($client->getResponse(), 400);
    // }

    /**
     * Create a nicely formatted json user
     *
     * @param string $title
     * @return string
     */
    protected function createJsonUser($firstName)
    {
        $json = '{"' . 'user' . '": {"firstName":"' . $firstName . '"}}';
        return  $json;
    }
}
