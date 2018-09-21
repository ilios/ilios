<?php

namespace App\Service;

/**
 * Class Archive
 * Put PharData behind a testable interface
 */
class Archive
{
    public static function extract(string $source, string $destination)
    {
        $phar = new \PharData($source);
        $phar->extractTo($destination);
    }
}
