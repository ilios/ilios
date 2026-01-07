<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\SecretManager;
use App\Tests\TestCase;

final class SecretManagerTest extends TestCase
{
    public function testGetSecret(): void
    {
        $service = new SecretManager('secret', 'transition-secret');
        $this->assertSame('secret', $service->getSecret());
    }

    public function testGetTransitionalSecret(): void
    {
        $service = new SecretManager('secret', 'transition-secret');
        $this->assertSame('transition-secret', $service->getTransitionalSecret());
    }

    public function testGetTransitionalSecretCanBeNull(): void
    {
        $service = new SecretManager('secret', null);
        $this->assertNull($service->getTransitionalSecret());
    }
}
