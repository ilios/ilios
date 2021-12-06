<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;

/**
 * Class FileSystem
 *
 */
class TemporaryFileSystem
{
    protected string $temporaryFileStorePath;

    public function __construct(protected SymfonyFileSystem $fileSystem, string $kernelProjectDir)
    {
        $tmpPath = realpath($kernelProjectDir . '/var/tmp');
        $this->temporaryFileStorePath = $tmpPath . '/uploads';
        if (!$fileSystem->exists($this->temporaryFileStorePath)) {
            $fileSystem->mkdir($this->temporaryFileStorePath);
        }
    }

    /**
     * Store a file and return the hash
     * @return string $hash
     */
    public function storeFile(File $file): string
    {
        $hash = md5_file($file->getPathname());
        if (!$this->fileSystem->exists($this->getPath($hash))) {
            $this->fileSystem->rename(
                $file->getPathname(),
                $this->getPath($hash)
            );
        }

        return $hash;
    }

    /**
     * Create a temporary file from a string
     */
    public function createFile(string $contents): File
    {
        $hash = md5($contents);
        $path = $this->getPath($hash);
        if (!$this->fileSystem->exists($path)) {
            $this->fileSystem->dumpFile($path, $contents);
        }

        return $this->getFile($hash);
    }

    /**
     * Remove a file from the file system by hash
     * @param string $hash
     */
    public function removeFile($hash)
    {
        $this->fileSystem->remove($this->getPath($hash));
    }

    /**
     * Get a File from a hash
     * @param string $hash
     * @return File|bool
     */
    public function getFile($hash): File|bool
    {
        if ($this->fileSystem->exists($this->getPath($hash))) {
            return new File($this->getPath($hash));
        }

        return false;
    }

    /**
     * Turn a relative path into an ilios file store path
     * @param  string $hash
     * @return string
     */
    protected function getPath($hash): string
    {
        return $this->temporaryFileStorePath . '/' . $hash;
    }
}
