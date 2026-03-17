<?php

declare(strict_types=1);

namespace App\Tests\Monitor;

use App\Monitor\PhpExtension;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use App\Tests\TestCase;

/**
 * @covers \App\Monitor\PhpExtension
 */
final class PhpExtensionTest extends TestCase
{
    public function testLabel(): void
    {
        $check = new PhpExtension([]);
        $this->assertEquals('PHP extensions', $check->getLabel());
    }

    public function testCheckSucceeds(): void
    {
        // These extensions are listed as required in `composer.json`, so they should be present.
        $extensions = ['curl', 'json'];
        $check = new PhpExtension($extensions);
        $result = $check->check();
        $this->assertInstanceOf(Success::class, $result);
        $this->assertEquals('curl, json PHP extensions loaded.', $result->getMessage());
    }

    public function testCheckFails(): void
    {
        $extensions = ['curl', 'json', 'geflarknik', 'zipflop'];
        $check = new PhpExtension($extensions);
        $result = $check->check();
        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('geflarknik, zipflop PHP extensions not loaded.', $result->getMessage());
    }
}
