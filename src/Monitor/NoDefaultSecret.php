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
    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
     */
    public function check()
    {
        $secret = getenv('ILIOS_SECRET', true);
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
                "\nILIOS_SECRET: Set to a ${secret}. This isn't safe and should be changed"
            );
        }

        return new Success('ILIOS_SECRET uses a non-default value');
    }

    /**
     * Return a label describing this test instance.
     *
     * @return string
     */
    public function getLabel()
    {
        return 'No Default Secret';
    }
}
