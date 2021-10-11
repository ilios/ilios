<?php

declare(strict_types=1);

namespace App\Tests;

use Shivas\VersioningBundle\Provider\ProviderInterface;

class TestVersionProvider implements ProviderInterface
{
    public const VERSION = '0.0.99-test';

    public function __construct(protected string $environment)
    {
    }

    public function isSupported(): bool
    {
        return $this->environment === 'test';
    }

    public function getVersion(): string
    {
        return self::VERSION;
    }
}
