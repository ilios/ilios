<?php

declare(strict_types=1);

namespace App\Tests\Routing;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthRouteTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $client->request('GET', '/ilios/health/');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('liip_monitor_health_interface');
        $this->assertSelectorTextContains('h1', 'System Health Status');
    }
}
