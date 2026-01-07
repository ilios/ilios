<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class SecretLength implements CheckInterface
{
    private const string NAME = 'ILIOS_SECRET';
    private const string UPDATE_URL = 'https://github.com/ilios/ilios/blob/master/docs/update.md';

    /**
     * Minimum length based on
     * https://github.com/firebase/php-jwt/blob/66f3decac70559c394b286e73bb133989f2859e1/src/JWT.php#L704-L711
     * 256 - (JsonWebTokenManager::PREPEND_KEY_LENGTH * 8)
     */
    public const int MINIMUM_LENGTH = 18;


    /**
     * Ensure ILIOS_SECRET is long enough
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

        if (strlen($secret) < self::MINIMUM_LENGTH) {
            return new Warning(
                "\nILIOS_SECRET: Secret is too short. It should be at least 18 characters long."
                . "\nFor help see: " . self::UPDATE_URL
            );
        }

        return new Success('ILIOS_SECRET is long enough');
    }

    /**
     * Describe this test
     */
    public function getLabel(): string
    {
        return 'Secret Length';
    }
}
