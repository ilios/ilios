<?php

namespace Ilios\AuthenticationBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;

class ConfigControllerTest extends WebTestCase
{
    use JsonControllerTest;
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/auth/config');

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $this->assertEquals(
            array('config' => array('type' => 'form')),
            json_decode($response->getContent(), true)
        );
    }
}
