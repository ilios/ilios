<?php

namespace App\Monitor;

use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

class RequiredENV implements CheckInterface
{
    const REQUIRED_ENV = [
        'APP_ENV',
        'ILIOS_DATABASE_URL',
        'ILIOS_DATABASE_MYSQL_VERSION',
        'ILIOS_MAILER_URL',
        'ILIOS_LOCALE',
        'ILIOS_SECRET'
    ];
    const INSTRUCTIONS_URL = 'https://github.com/ilios/ilios/blob/master/docs/env_vars_and_config.md';

    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
     */
    public function check()
    {
        $missingVariables = array_filter(self::REQUIRED_ENV, function ($name) {
            return !getenv($name);
        });

        if (count($missingVariables)) {
            $missing = implode("\n", $missingVariables);

            return new Failure(
                "\nMissing:\n" . $missing . "\n For help see: \n " . self::INSTRUCTIONS_URL
            );
        }
        return new Success('All required ENV variables are setup');
    }

    /**
     * Return a label describing this test instance.
     *
     * @return string
     */
    public function getLabel()
    {
        return 'ENV variables';
    }
}
