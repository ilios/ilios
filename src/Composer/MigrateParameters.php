<?php

declare(strict_types=1);

namespace App\Composer;

use Composer\Script\Event;
use Exception;
use Symfony\Component\Yaml\Yaml;

class MigrateParameters
{
    private const array SYMFONY_KEYS = [
        'database_driver',
        'database_host',
        'database_port',
        'database_name',
        'database_user',
        'database_password',
        'database_mysql_version',
        'mailer_transport',
        'mailer_host',
        'mailer_user',
        'mailer_password',
        'locale',
        'secret',
    ];
    public static function migrate(Event $event): void
    {
        $io = $event->getIO();

        $appPath = realpath(__DIR__ . '/../../app');
        $parametersPath = $appPath . '/config/parameters.yml';
        $parameters = self::readParameters($parametersPath);
        $changed = 0;
        foreach (self::SYMFONY_KEYS as $key) {
            if (array_key_exists($key, $parameters)) {
                $value = $parameters[$key];
                unset($parameters[$key]);
                $newKey = 'env(ILIOS_' . strtoupper($key) . ')';
                $parameters[$newKey] = $value;
                $changed++;
            }
        }
        if ($changed > 0) {
            $io->write(sprintf('<info>Converting parameters found at %s</info>', $parametersPath));
            self::writeParameters($parametersPath, $parameters);
            $io->write(sprintf('<info>Converted %s parameters</info>', $changed));
        }
    }

    /**
     * Read existing parameters
     */
    protected static function readParameters(string $parametersPath): array
    {
        if (is_readable($parametersPath)) {
            $parameters = Yaml::parse(file_get_contents($parametersPath));
            if (array_key_exists('parameters', $parameters)) {
                return $parameters['parameters'];
            }
        }
        return [];
    }

    /**
     * Write parameters to the file
     * @throws Exception
     */
    protected static function writeParameters(string $parametersPath, mixed $parameters): void
    {
        if (!is_writable($parametersPath)) {
            throw new Exception("Unable to write parameters file at {$parametersPath}");
        }

        $string = Yaml::dump(['parameters' => $parameters]);
        file_put_contents($parametersPath, $string);
    }
}
