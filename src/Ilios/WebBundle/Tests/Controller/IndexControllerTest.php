<?php

namespace Ilios\WebBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();
        $content = $response->getContent();

        $container = $client->getContainer();
        $builder = $container->get('iliosweb.jsonindex');
        $text = $builder->getIndex('prod');

        $this->assertSame($text, $content);

        //ensure we have a 60 second max age
        $this->assertSame(60, $response->getMaxAge());
    }
}
