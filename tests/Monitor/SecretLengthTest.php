<?php

declare(strict_types=1);

namespace App\Tests\Monitor;

use App\Monitor\SecretLength;
use App\Service\JsonWebTokenManager;
use App\Tests\TestCase;

final class SecretLengthTest extends TestCase
{
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
}
