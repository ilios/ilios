<?php

namespace App\Monitor;

use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;

class PhpConfiguration implements CheckInterface
{
    /**
     * @inheritdoc
     */
    public function check()
    {
        $gtOptions = [
            'opcache.memory_consumption' => 256,
            'opcache.max_accelerated_files' => 20000,
            'realpath_cache_ttl' => 600,
        ];

        foreach ($gtOptions as $option => $required) {
            $value = (int) ini_get($option);
            if ($value < $required) {
                return new Warning(
                    "`${option}` setting is too low, should be at least `${required}`"
                );
            }
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

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return 'PHP Configuration';
    }

    /**
     * Can't believe there isn't a builtin for this.
     * I stole this from https://stackoverflow.com/a/44767616/796999 thanks!
     * @param string $value
     * @return int
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
