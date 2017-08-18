<?php

namespace Ilios\CliBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Yaml\Yaml;

class MigrateParameters
{
    const SYMFONY_KEYS = [
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
        'secret'
    ];
    public static function migrate(Event $event)
    {
        $io = $event->getIO();
        $extras = $event->getComposer()->getPackage()->getExtra();

        if (!isset($extras['symfony-app-dir'])) {
            throw new \InvalidArgumentException(
                'The parameter migrator needs to be configured through the extra.symfony-app-dir setting.'
            );
        }
        $appPath = $extras['symfony-app-dir'];
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
     * @return array
     */
    protected static function readParameters($parametersPath)
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
     * @param $parameters
     * @throws \Exception
     */
    protected static function writeParameters($parametersPath, $parameters)
    {
        if (!is_writable($parametersPath)) {
            throw new \Exception("Unable to write parameters file at ${parametersPath}");
        }

        $string = Yaml::dump(['parameters' => $parameters]);
        file_put_contents($parametersPath, $string);
    }
}
