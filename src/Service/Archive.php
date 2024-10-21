<?php

declare(strict_types=1);

namespace App\Service;

use Archive_Tar;

/**
 * Class Archive
 * Put PharData behind a testable interface
 */
class Archive
{
    public static function extract(string $source, string $destination): void
    {
        $tar = new Archive_Tar($source, true);
        $tar->extract($destination);
    }
}
