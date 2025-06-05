<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\Config;
use App\Service\TikaFactory;
use App\Tests\TestCase;
use Mockery as m;
use Vaites\ApacheTika\Client;
use Vaites\ApacheTika\Clients\WebClient;

final class TikaFactoryTest extends TestCase
{
    protected m\MockInterface|Config $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = m::mock(Config::class);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->config);
    }

    public function testCreate(): void
    {
        $this->config->shouldReceive('get')
            ->once()->with('tika_url')->andReturn('https://tika.com');
        $client = TikaFactory::getClient($this->config);
        $this->assertInstanceOf(WebClient::class, $client);
        $this->assertSame('https://tika.com:9998', $client->getUrl());
    }

    public function testNothingWithNoUrl(): void
    {
        $this->config->shouldReceive('get')
            ->once()->with('tika_url')->andReturn(null);
        $client = TikaFactory::getClient($this->config);
        $this->assertNull($client);
    }
}
