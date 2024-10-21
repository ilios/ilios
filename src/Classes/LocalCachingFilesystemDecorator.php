<?php

declare(strict_types=1);

namespace App\Classes;

use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;

/**
 * Add a local file cache on top of our remote filesystem
 * to cache the file which and avoid going back
 * and forth to S3 a bunch.
 */
class LocalCachingFilesystemDecorator implements FilesystemOperator
{
    private FilesystemOperator $cacheFileSystem;
    private FilesystemOperator $remoteFileSystem;
    protected bool $cacheEnabled;

    public function __construct(FilesystemOperator $cacheFileSystem, FilesystemOperator $remoteFileSystem)
    {
        $this->cacheFileSystem = $cacheFileSystem;
        $this->remoteFileSystem = $remoteFileSystem;
        $this->cacheEnabled = true;
    }

    /**
     * Temporarily disable the cache
     */
    public function disableCache(): void
    {
        $this->cacheEnabled = false;
    }

    /**
     * Re-enable the cache
     */
    public function enableCache(): void
    {
        $this->cacheEnabled = true;
    }

    /**
     * Check if the cache is enabled
     */
    public function isCacheEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    /**
     * Wrapped delete for removing possibly missing files from the local cache
     */
    protected function deleteFromCache(string $path): void
    {
        try {
            //cleanup any existing test file
            $this->cacheFileSystem->delete($path);
        } catch (FilesystemException | UnableToDeleteFile) {
            //ignore this one we don't always have files in the cache
        }
    }

    /**
     * Wrapped deleteFromDir for removing possibly missing files from the local cache
     */
    protected function deleteDirectoryFromCache(string $dirname): void
    {
        try {
            //cleanup any existing test file
            $this->cacheFileSystem->deleteDirectory($dirname);
        } catch (FilesystemException | UnableToDeleteDirectory) {
            //ignore this one we don't always have files in the cache
        }
    }

    public function fileExists(string $location): bool
    {
        return $this->remoteFileSystem->fileExists($location);
    }

    public function directoryExists(string $location): bool
    {
        return $this->remoteFileSystem->directoryExists($location);
    }

    public function has(string $location): bool
    {
        return $this->remoteFileSystem->has($location);
    }

    public function read(string $location): string
    {
        if ($this->cacheEnabled && $this->cacheFileSystem->fileExists($location)) {
            return $this->cacheFileSystem->read($location);
        }
        $result = $this->remoteFileSystem->read($location);

        $this->cacheFileSystem->write($location, $result);
        return $result;
    }

    public function readStream(string $location): mixed
    {
        if ($this->cacheEnabled && $this->cacheFileSystem->fileExists($location)) {
            return $this->cacheFileSystem->readStream($location);
        }
        $result = $this->remoteFileSystem->readStream($location);

        $this->cacheFileSystem->writeStream($location, $result);
        return $result;
    }

    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        return $this->remoteFileSystem->listContents($location, $deep);
    }

    public function lastModified(string $path): int
    {
        return $this->remoteFileSystem->lastModified($path);
    }

    public function fileSize(string $path): int
    {
        return $this->remoteFileSystem->fileSize($path);
    }

    public function mimeType(string $path): string
    {
        return $this->remoteFileSystem->mimeType($path);
    }

    public function visibility(string $path): string
    {
        return $this->remoteFileSystem->visibility($path);
    }

    public function write(string $location, string $contents, array $config = []): void
    {
        $this->remoteFileSystem->write($location, $contents, $config);
        if ($this->cacheEnabled) {
            $this->cacheFileSystem->write($location, $contents, $config);
        }
    }

    public function writeStream(string $location, mixed $contents, array $config = []): void
    {
        $this->remoteFileSystem->writeStream($location, $contents, $config);
        if ($this->cacheEnabled) {
            $this->cacheFileSystem->writeStream($location, $contents, $config);
        }
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $this->remoteFileSystem->setVisibility($path, $visibility);
    }

    public function delete(string $location): void
    {
        $this->deleteFromCache($location);
        $this->remoteFileSystem->delete($location);
    }

    public function deleteDirectory(string $location): void
    {
        $this->deleteDirectoryFromCache($location);
        $this->remoteFileSystem->deleteDirectory($location);
    }

    public function createDirectory(string $location, array $config = []): void
    {
        $this->remoteFileSystem->createDirectory($location, $config);
    }

    public function move(string $source, string $destination, array $config = []): void
    {
        $this->deleteFromCache($source);
        $this->remoteFileSystem->move($source, $destination, $config);
    }

    public function copy(string $source, string $destination, array $config = []): void
    {
        $this->remoteFileSystem->copy($source, $destination, $config);
    }
}
