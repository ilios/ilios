<?php

namespace Ilios\CoreBundle\Service;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;

/**
 * Class Filesystem
 *
 * Extends Symfonys built in file system to add a read method which symfony core devs
 * do not wish to include in the base class.
 *
 *
 */
class Filesystem extends SymfonyFileSystem
{

    /**
     * Read the contents of a file and return it as a string
     *
     * @param $filename
     *
     * @return string
     *
     * @throws IOException
     */
    public function readFile($filename)
    {
        $contents = file_get_contents($filename);
        if (false === $contents) {
            throw new IOException("Unable to read file " . $filename);
        }

        return $contents;
    }
}
