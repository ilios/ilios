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
        $version = $container->getParameter('ilios_web.version');
        $url = $container->getParameter('ilios_web.bucket_url');
        $fileName = $version?'index.html:' . $version:'index.html';
        $text = file_get_contents($url . $fileName, false);

        $this->assertSame($text, $content);

        //ensure we have a 60 second max age
        $this->assertSame(60, $response->getMaxAge());
    }
}
