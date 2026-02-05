<?php

declare(strict_types=1);

namespace App\Tests\Routing;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SwaggerRouteTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/doc');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app.swagger_ui');
    }
}
