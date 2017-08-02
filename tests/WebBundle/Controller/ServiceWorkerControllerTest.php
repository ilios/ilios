<?php

namespace Tests\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceWorkerControllerTest extends WebTestCase
{
    public function testSwJs()
    {
        $client = static::createClient();
        $client->request('GET', '/sw.js');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode(), substr($response->getContent(), 0, 500));
    }
    public function testSwRegistrationJs()
    {
        $client = static::createClient();
        $client->request('GET', '/sw-registration.js');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode(), substr($response->getContent(), 0, 500));
    }
}
