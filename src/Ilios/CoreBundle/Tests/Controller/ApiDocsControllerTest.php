<?php

namespace Ilios\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;

class ApiDocsControllerTest extends WebTestCase
{
    /**
     * Ensure that the page will load correctly
     * it fails when there are major issues with the API docs block comments
     * @group controllers_a
     */
    public function testLoad()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/doc');

        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode(), $response->getContent());
        
        $this->assertEquals('Ilios API', $crawler->filter('title')->text());
        
        $this->assertGreaterThan(40, $crawler->filter('.section')->count(), 'API Endpoints loaded');
    }
}
