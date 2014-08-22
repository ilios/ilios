<?php

namespace Ilios\CoreBundle\Tests\Controller;

class CurrentSessionControllerTest extends ApiTestCase
{
    public function setup()
    {
        $this->loadFixtures(
            array(
                'Ilios\CoreBundle\Tests\Fixtures\LoadUserData'
            )
        );
    }

    public function testJsonGetCurrentSessionAction()
    {
        $userId = 1;
        $client = $this->createJsonRequest(
            'GET',
            $this->getUrl('api_1_get_currentsession'),
            null,
            $userId
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);

        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['currentsession']['userId']), $content);
        $this->assertEquals($userId, $decoded['currentsession']['userId'], $content);
    }

    public function testJsonGetNotFoundSessionAction()
    {
        $client = $this->createJsonRequest(
            'GET',
            $this->getUrl('api_1_get_currentsession')
        );

        $response = $client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
