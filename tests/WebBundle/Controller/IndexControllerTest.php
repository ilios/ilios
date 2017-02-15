<?php

namespace Tests\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();

        //ensure we have a 60 second max age
        $this->assertSame(60, $response->getMaxAge());
    }
}
