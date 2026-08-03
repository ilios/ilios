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
    public function testCheckSucceeds(string $version): void
    {
        $composerFilePath = __DIR__ . '/TESTFILES/composer.json';
        $check = new PhpVersion($version, $composerFilePath);
        $result = $check->check();
        $this->assertInstanceOf(Success::class, $result);
        $this->assertEquals(
            "The current PHP version '{$version}' meets the expected version requirement '>= 8.5'",
            $result->getMessage()
        );
    }

    public static function checkSucceedsProvider(): array
    {
        return [
            ['8.5'],
            ['8.5.0'],
            ['8.5.1'],
            ['8.6'],
            ['9'],
        ];
    }
    #[DataProvider('checkFailsProvider')]
    public function testCheckFails(string $version): void
    {
        $composerFilePath = __DIR__ . '/TESTFILES/composer.json';
        $check = new PhpVersion($version, $composerFilePath);
        $result = $check->check();
        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals(
            "The current PHP version '{$version}' doesn't meet the expected version requirement '>= 8.5'",
            $result->getMessage()
        );
    }

    public static function checkFailsProvider(): array
    {
        return [
            ['7'],
            ['8'],
            ['8.4'],
            ['8.4.0'],
        ];
    }

    public function testCheckFailsIfComposerFileCannotBeRead(): void
    {
        $composerFilePath = __DIR__ . '/TESTFILES/not-a-file.json';
        $check = new PhpVersion('whatever', $composerFilePath);
        $result = $check->check();
        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals(
            "Unable to read file contents of the given composer file",
            $result->getMessage()
        );
    }

    public function testCheckFailsIfComposerFileCannotBeDecoded(): void
    {
        $composerFilePath = __DIR__ . '/TESTFILES/dummy.txt';
        $check = new PhpVersion('whatever', $composerFilePath);
        $result = $check->check();
        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals(
            "Unable to decode the given composer file",
            $result->getMessage()
        );
    }

    public function testCheckFailsIfComposerDoesntDeclarePhpVersionRequirement(): void
    {
        $composerFilePath = __DIR__ . '/TESTFILES/empty.composer.json';
        $check = new PhpVersion('whatever', $composerFilePath);
        $result = $check->check();
        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals(
            "Unable to find the PHP version requirement in the given composer file",
            $result->getMessage()
        );
    }
}
