<?php

declare(strict_types=1);

namespace App\Tests\Monitor;

use App\Monitor\SecretLength;
use App\Service\JsonWebTokenManager;
use App\Tests\TestCase;
use Laminas\Diagnostics\Result\Warning;

final class SecretLengthTest extends TestCase
{
    protected string | bool $originalSecret;
    protected ?string $originalEnvSecret;
    protected SecretLength $subject;

    public function setup(): void
    {
        $this->subject = new SecretLength();
        $this->originalSecret = getenv('ILIOS_SECRET');
        $this->originalEnvSecret = $_ENV['ILIOS_SECRET'];
    }

    public function tearDown(): void
    {
        putenv('ILIOS_SECRET=' . $this->originalSecret);
        $_ENV['ILIOS_SECRET'] = $this->originalEnvSecret;
        unset($this->subject);
    }

    public function testLengthValidation(): void
    {
        /**
         * The validity of the key is based on the algorithm we've selected for signing.
         * This test ensures that if we update the key signing algorithm we will
         * also update the required length of the secret.
         */
        $minimumKeyLength = (256 - (strlen(JsonWebTokenManager::PREPEND_KEY) * 8)) / 8;
        $this->assertGreaterThanOrEqual($minimumKeyLength, SecretLength::MINIMUM_LENGTH);
        $this->assertEquals(18, SecretLength::MINIMUM_LENGTH);
        $this->assertEquals('HS256', JsonWebTokenManager::SIGNING_ALGORITHM);
        $this->assertEquals('ilios.jwt.key.', JsonWebTokenManager::PREPEND_KEY);
    }

    public function testCheckNotSet(): void
    {
        putenv('ILIOS_SECRET');
        $_ENV['ILIOS_SECRET'] = false;
        $result = $this->subject->check();
        $this->assertInstanceOf(Warning::class, $result);
        $this->assertStringContainsString('not set', $result->getMessage());
    }

    public function testCheckTooShort(): void
    {
        putenv('ILIOS_SECRET=short');
        $result = $this->subject->check();
        $this->assertInstanceOf(Warning::class, $result);
        $this->assertStringContainsString('too short', $result->getMessage());
    }
}
