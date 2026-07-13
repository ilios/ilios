<?php

declare(strict_types=1);

namespace App\Tests\Monitor;

use App\Monitor\PhpVersion;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use App\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(PhpVersion::class)]
final class PhpVersionTest extends TestCase
{
    public function testLabel(): void
    {
        $check = new PhpVersion(PHP_VERSION, PHP_VERSION);
        $this->assertEquals('PHP version', $check->getLabel());
    }

    #[DataProvider('checkSucceedsProvider')]
    public function testCheckSucceeds(string $version, string $minimumSupportedVersion): void
    {
        $check = new PhpVersion($version, $minimumSupportedVersion);
        $result = $check->check();
        $this->assertInstanceOf(Success::class, $result);
        $this->assertEquals(
            'The current PHP version matches or exceeds the expected minimum version.',
            $result->getMessage()
        );
    }

    public static function checkSucceedsProvider(): array
    {
        return [
            [PHP_VERSION, PHP_VERSION],
            ['8', '8'],
            ['8.0', '8'],
            ['8.0.0', '8'],
            ['8.4', '8.4'],
            ['8.4.0', '8.4.0'],
            ['9', '8'],
            ['8.5', '8.4'],
            ['8.4.1', '8.4.0'],
        ];
    }
    #[DataProvider('checkFailsProvider')]
    public function testCheckFails(string $version, string $minimumSupportedVersion): void
    {
        $check = new PhpVersion($version, $minimumSupportedVersion);
        $result = $check->check();
        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals(
            'The current PHP version is older than the expected version.',
            $result->getMessage()
        );
    }

    public static function checkFailsProvider(): array
    {
        return [
            ['7', '8'],
            ['8.0', '8.1'],
            ['8.1.0', '8.1.1'],
        ];
    }
}
