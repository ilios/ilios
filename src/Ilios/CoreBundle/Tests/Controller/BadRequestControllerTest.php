<?php

namespace Ilios\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BadRequestControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/api/nothing');
        $response = $client->getResponse();
        $this->assertEquals(
            404,
            $response->getStatusCode()
        );

    }
}
