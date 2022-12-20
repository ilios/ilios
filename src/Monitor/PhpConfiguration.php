<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class PhpConfiguration implements CheckInterface
{
    public function check(): ResultInterface
    {
        $opcacheEnabled = (extension_loaded('Zend OPcache') && ini_get('opcache.enable'));
        if (!$opcacheEnabled) {
            return new Failure(
                "Install or enable `opcache` a PHP accelerator."
            );
        }
        $gtOptions = [
            'opcache.memory_consumption' => 256,
            'opcache.max_accelerated_files' => 20000,
            'realpath_cache_ttl' => 600,
        ];

        foreach ($gtOptions as $option => $required) {
            $value = (int) ini_get($option);
            if ($value < $required) {
                return new Warning(
                    "`{$option}` set to `{$value}`. That is too low, should be at least `{$required}`"
                );
            }
        }
        $maxExecutionTimeConfig = (int) ini_get('max_execution_time');
        if ($maxExecutionTimeConfig !== 0 && $maxExecutionTimeConfig < 300) {
            return new Warning(
                "`max_execution_time` set to `{$maxExecutionTimeConfig}`. " .
                "That is too low, should be at least `300`"
            );
        }
        $realPathCacheSizeConfig = ini_get('realpath_cache_size');
        $value = $this->valueToBytes($realPathCacheSizeConfig);
        if ($value < 4194304) {
            return new Warning(
                "`realpath_cache_size` setting is too low, should be at least `4096K`"
            );
        }

        $variablesOrder = ini_get('variables_order');
        if ($variablesOrder !== 'EGPCS') {
            return new Failure(
                "`variables_order` setting is wrong, it should be `EGPCS`"
            );
        }

        return new Success('is correct');
    }

    public function getLabel(): string
    {
        return 'PHP Configuration';
    }

    /**
     * Can't believe there isn't a builtin for this.
     * I stole this from https://stackoverflow.com/a/44767616/796999 thanks!
     */
    protected function valueToBytes(string $value): int
    {
        preg_match('/^(?<value>\d+)(?<option>[K|M|G]*)$/i', $value, $matches);

        $value = (int) $matches['value'];
        $option = strtoupper($matches['option']);

        if ($option) {
            if ($option === 'K') {
                $value *= 1024;
            } elseif ($option === 'M') {
                $value *= 1024 * 1024;
            } elseif ($option === 'G') {
                $value *= 1024 * 1024 * 1024;
            }
        }

        return $value;
    }
}
