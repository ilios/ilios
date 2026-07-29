<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Composer\Semver\Semver;

/**
 * Validates PHP version.
 *
 * This check compares two PHP versions against each other.
 * If the given PHP version is equal to or newer than the expected minimum PHP version then the check succeeds.
 */
class PhpVersion implements CheckInterface
{
    /**
     * @param string $version The PHP version to check.
     * @param string $composerFilePath Path to the composer.json file that declares the minimum required PHP version.
     */
    public function __construct(protected string $version, protected string $composerFilePath)
    {
    }

    public function check(): ResultInterface
    {
        $contents = @file_get_contents($this->composerFilePath);
        if (!$contents) {
            return new Failure("Unable to read file contents of the given composer file");
        }
        $json = @json_decode($contents, true);
        if (is_null($json)) {
            return new Failure("Unable to decode the given composer file");
        }

        $expected = array_key_exists('require', $json) && array_key_exists('php', $json['require'])
            ? $json['require']['php']
            : false;
        if (!$expected) {
            return new Failure("Unable to find the PHP version requirement in the given composer file");
        }

        if (!Semver::satisfies($this->version, $expected)) {
            return new Failure(
                "The current PHP version '{$this->version}' doesn't meet the expected version requirement '{$expected}'"
            );
        }

        return new Success(
            "The current PHP version '{$this->version}' meets the expected version requirement '{$expected}'"
        );
    }

    public function getLabel(): string
    {
        return 'PHP version';
    }
}
