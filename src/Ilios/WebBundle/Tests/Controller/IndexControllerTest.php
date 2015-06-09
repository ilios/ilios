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
        $text = file_get_contents('https://s3-us-west-1.amazonaws.com/iliosindex/index.html', false);

        $this->assertSame($text, $content);

    }
}
