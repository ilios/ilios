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
}
