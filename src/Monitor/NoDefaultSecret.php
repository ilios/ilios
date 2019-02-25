<?php

namespace App\Monitor;

use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;

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

        return new Success('ILIOS_SECRET not set do a default value');
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
