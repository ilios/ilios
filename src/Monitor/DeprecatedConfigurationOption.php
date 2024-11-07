<?php

declare(strict_types=1);

namespace App\Monitor;

use App\Service\Config;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class DeprecatedConfigurationOption implements CheckInterface
{
    public function __construct(protected Config $config)
    {
    }

    // key is the option, value is whether to fail [true] or warn [false] when the value is present.
    private const array DEPRECATED_CONFIG = [
        'enable_tracking' => false,
        'tracking_code' => false,
        'elasticsearch_hosts' => true,
        'elasticsearch_upload_limit' => true,
    ];
    private const string INSTRUCTIONS_URL = 'https://github.com/ilios/ilios/blob/master/docs/env_vars_and_config.md';
    private const string UPDATE_URL = 'https://github.com/ilios/ilios/blob/master/docs/update.md';

    public function check(): ResultInterface
    {
        $deprecatedOptions = [];
        foreach (self::DEPRECATED_CONFIG as $key => $shouldFail) {
            $value = $this->config->get($key);
            if (!is_null($value)) {
                if ($shouldFail) {
                    return new Failure(
                        "'{$key}' has been removed."
                        . $this->getUpdateDocs()
                    );
                } else {
                    $deprecatedOptions[] = $key;
                }
            }
        }
        if ($deprecatedOptions !== []) {
            $message = "\n " .
                implode("\n ", $deprecatedOptions) .
                "\nhave been deprecated and will be removed soon.\n";

            return new Warning($message . $this->getUpdateDocs());
        }

        return new Success('All required ENV variables are setup');
    }

    protected function getUpdateDocs(): string
    {
        $warnings[] = "\nFor help see: " . self::UPDATE_URL;
        $warnings[] = "For information on supported variables see: " . self::INSTRUCTIONS_URL;
        return implode("\n", $warnings) . "\n";
    }

    public function getLabel(): string
    {
        return 'Configuration Options';
    }
}
