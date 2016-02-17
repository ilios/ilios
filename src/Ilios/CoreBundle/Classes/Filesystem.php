<?php

namespace Ilios\CoreBundle\Classes;

use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use \IOError;
/**
 * Class Filesystem
 *
 * Extends Symfonys built in file system to add a read method which symfony core devs
 * do not wish to include in the base class.
 *
 * @package Ilios\CoreBundle\Classes
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
     * @throws IOError
     */
    public function readFile($filename)
    {
        $contents = file_get_contents($filename);
        if (false === $contents) {
            throw new IOError("Unable to read file " . $filename);
        }

        return $contents;
    }
}
