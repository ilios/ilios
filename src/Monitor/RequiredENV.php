<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

class RequiredENV implements CheckInterface
{
    private const array REQUIRED_ENV = [
        'ILIOS_DATABASE_URL',
        'ILIOS_LOCALE',
        'ILIOS_SECRET',
        'MAILER_DSN',
    ];
    private const string INSTRUCTIONS_URL = 'https://github.com/ilios/ilios/blob/master/docs/env_vars_and_config.md';
    private const string UPDATE_URL = 'https://github.com/ilios/ilios/blob/master/docs/update.md';

    /**
     * Perform the actual check and return a ResultInterface
     */
    public function check(): ResultInterface
    {
        $missingVariables = array_filter(self::REQUIRED_ENV, fn($name) => !getenv($name) && !isset($_ENV[$name]));

        if ($missingVariables !== []) {
            $missing = implode("\n", $missingVariables);

            return new Failure(
                "\nMissing:\n" . $missing . "\n For help see: \n " . self::INSTRUCTIONS_URL
            );
        }

        if (getenv('ILIOS_DATABASE_MYSQL_VERSION') || isset($_ENV['ILIOS_DATABASE_MYSQL_VERSION'])) {
            return new Failure(
                "\nILIOS_DATABASE_MYSQL_VERSION should be migrated. See \n " . self::UPDATE_URL .
                " for details.\n"
            );
        }
        return new Success('All required ENV variables are setup');
    }

    /**
     * Return a label describing this test instance.
     */
    public function getLabel(): string
    {
        return 'ENV variables';
    }
}
