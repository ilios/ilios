<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

use function version_compare;

/**
 * Validates PHP version.
 *
 * This check compares two PHP versions against each other.
 * If the given PHP version is equal to or newer than the expected minimum PHP version then the check succeeds.
 */
class PhpVersion implements CheckInterface
{
    public const string COMPARISON_OPERATOR = '>=';

    /**
     * @param string $version The given PHP version.
     * @param string $minimumSupportedVersion The minimum supported PHP version to check against.
     */
    public function __construct(protected string $version, protected string $minimumSupportedVersion)
    {
    }

    public function check(): ResultInterface
    {
        if (!version_compare($this->version, $this->minimumSupportedVersion, self::COMPARISON_OPERATOR)) {
            return new Failure('The current PHP version is older than the expected version.');
        }
        return new Success('The current PHP version matches or exceeds the expected minimum version.');
    }

    public function getLabel(): string
    {
        return 'PHP version';
    }
}
