<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class NoDefaultSecret implements CheckInterface
{
    private const string NAME = 'ILIOS_SECRET';

    /**
     * Ensure ILIOS_SECRET isn't set to a default value
     */
    public function check(): ResultInterface
    {
        $secret = getenv(self::NAME);
        if (!$secret && isset($_ENV[self::NAME])) {
            $secret = $_ENV[self::NAME];
        }

        if (!$secret) {
            return new Warning("'ILIOS_SECRET' is not set");
        }

        $badSecrets = array_map('strtolower', [
            'NotSecretChangeMe',
            'ThisTokenIsNotSoSecretChangeIt',
            'ST@G1nGS3CRET12345',
            'PR0DUCT10nS3CRET12345',
        ]);

        if (in_array(trim(strtolower($secret)), $badSecrets)) {
            return new Failure(
                "\nILIOS_SECRET: Set to a {$secret}. This isn't safe and should be changed"
            );
        }

        return new Success('ILIOS_SECRET uses a non-default value');
    }

    /**
     * Describe this test
     */
    public function getLabel(): string
    {
        return 'No Default Secret';
    }
}
