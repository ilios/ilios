<?php

/**
 * Utility class providing helper methods for dealing with files.
 *
 * @category Ilios
 * @package Ilios
 * @copyright Copyright (c) 2010-2014 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */

/**
 * Utility class providing helper methods for dealing with files.
 *
 * @category Ilios
 * @package Ilios
 * @copyright Copyright (c) 2010-2014 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */
class Ilios_FileUtils
{
    /**
     * Prints out a given file in chunks.
     *
     * @param string $filename Path to the file.
     * @return boolean TRUE on success, FALSE on failure.
     */
    public function streamFileContentsChunked ($filename)
    {
        $chunkSizeInBytes = 1024 * 1024;

        $handle = @fopen($filename, 'rb');

        if ($handle === false) {
            return false;
        }

        while (!feof($handle)) {
            $buffer = fread($handle, $chunkSizeInBytes);
            echo $buffer;
            ob_flush();
            flush();
        }

        fclose($handle);

        return true;
    }
}
