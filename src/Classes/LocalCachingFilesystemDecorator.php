<?php

declare(strict_types=1);

namespace App\Classes;

use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use Override;

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

    #[Override]
    public function fileExists(string $location): bool
    {
        return $this->remoteFileSystem->fileExists($location);
    }

    #[Override]
    public function directoryExists(string $location): bool
    {
        return $this->remoteFileSystem->directoryExists($location);
    }

    #[Override]
    public function has(string $location): bool
    {
        return $this->remoteFileSystem->has($location);
    }

    #[Override]
    public function read(string $location): string
    {
        if ($this->cacheEnabled && $this->cacheFileSystem->fileExists($location)) {
            return $this->cacheFileSystem->read($location);
        }
        $result = $this->remoteFileSystem->read($location);

        $this->cacheFileSystem->write($location, $result);
        return $result;
    }

    #[Override]
    public function readStream(string $location): mixed
    {
        if ($this->cacheEnabled && $this->cacheFileSystem->fileExists($location)) {
            return $this->cacheFileSystem->readStream($location);
        }
        $result = $this->remoteFileSystem->readStream($location);

        $this->cacheFileSystem->writeStream($location, $result);
        return $result;
    }

    #[Override]
    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        return $this->remoteFileSystem->listContents($location, $deep);
    }

    #[Override]
    public function lastModified(string $path): int
    {
        return $this->remoteFileSystem->lastModified($path);
    }

    #[Override]
    public function fileSize(string $path): int
    {
        return $this->remoteFileSystem->fileSize($path);
    }

    #[Override]
    public function mimeType(string $path): string
    {
        return $this->remoteFileSystem->mimeType($path);
    }

    #[Override]
    public function visibility(string $path): string
    {
        return $this->remoteFileSystem->visibility($path);
    }

    #[Override]
    public function write(string $location, string $contents, array $config = []): void
    {
        $this->remoteFileSystem->write($location, $contents, $config);
        if ($this->cacheEnabled) {
            $this->cacheFileSystem->write($location, $contents, $config);
        }
    }

    #[Override]
    public function writeStream(string $location, mixed $contents, array $config = []): void
    {
        $this->remoteFileSystem->writeStream($location, $contents, $config);
        if ($this->cacheEnabled) {
            $this->cacheFileSystem->writeStream($location, $contents, $config);
        }
    }

    #[Override]
    public function setVisibility(string $path, string $visibility): void
    {
        $this->remoteFileSystem->setVisibility($path, $visibility);
    }

    #[Override]
    public function delete(string $location): void
    {
        $this->deleteFromCache($location);
        $this->remoteFileSystem->delete($location);
    }

    #[Override]
    public function deleteDirectory(string $location): void
    {
        $this->deleteDirectoryFromCache($location);
        $this->remoteFileSystem->deleteDirectory($location);
    }

    #[Override]
    public function createDirectory(string $location, array $config = []): void
    {
        $this->remoteFileSystem->createDirectory($location, $config);
    }

    #[Override]
    public function move(string $source, string $destination, array $config = []): void
    {
        $this->deleteFromCache($source);
        $this->remoteFileSystem->move($source, $destination, $config);
    }

    #[Override]
    public function copy(string $source, string $destination, array $config = []): void
    {
        $this->remoteFileSystem->copy($source, $destination, $config);
    }
}
