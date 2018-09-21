<?php

namespace App\Composer;

use Composer\Script\Event;
use Composer\EventDispatcher\ScriptExecutionException;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Check that required ENV variables are set on the system and print an error if they are not.
 * @package App\Composer
 */
class CheckRequiredENV
{
    const REQUIRED_ENV = [
        'ILIOS_DATABASE_URL',
        'ILIOS_DATABASE_MYSQL_VERSION',
        'ILIOS_MAILER_URL',
        'ILIOS_LOCALE',
        'ILIOS_SECRET'
    ];
    const INSTRUCTIONS_URL = 'https://github.com/ilios/ilios/blob/master/docs/env_vars_and_config.md';
    public static function check(Event $event)
    {
        $io = $event->getIO();
        $hasError = false;
        $data = [];
        if (!isset($_SERVER['APP_ENV']) || in_array($_SERVER['APP_ENV'], ['dev', 'test'])) {
            $dotEnv = new Dotenv();
            $path = __DIR__ . '/../../.env';
            if (is_readable($path)) {
                $data = $dotEnv->parse(file_get_contents($path), $path);
            }
        }

        foreach (self::REQUIRED_ENV as $name) {
            if (!self::isEnvSet($data, $name)) {
                $hasError = true;
                $io->write(sprintf('<error>Required ENV variable %s is not set.</error>', $name));
            }
        }
        if ($hasError) {
            $io->write(
                sprintf(
                    '<warning>See %s for instructions for configuring missing ENV variables</warning>',
                    self::INSTRUCTIONS_URL
                )
            );
            throw new ScriptExecutionException();
        }
    }

    /**
     * See if there is an environmental variable for this var
     *
     * @param $data
     * @param $name
     * @return boolean
     */
    protected static function isEnvSet($data, $name)
    {
        $value = getenv($name);
        if (!$value) {
            return array_key_exists($name, $data);
        }

        return !!$value;
    }
}
