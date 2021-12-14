<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use scandir;

/**
 * Extends Symfonys built in file system to add methods useful for test mocks that symfony
 * does not wish to include in the base class.
 */
class Filesystem extends SymfonyFileSystem
{
    /**
     * Read the contents of a file and return it as a string
     */
    public function readFile(string $filename): string
    {
        $contents = file_get_contents($filename);
        if (false === $contents) {
            throw new IOException("Unable to read file " . $filename);
        }

        return $contents;
    }

    /**
     * List files and directories inside the specified path
     *
     * @throws IOException
     */
    public function scandir(string $directory): array
    {
        $rhett = scandir($directory);
        if (false === $rhett) {
            throw new IOException("Unable to examine directory " . $directory);
        }

        return $rhett;
    }
}
