<?php

declare(strict_types=1);

namespace App\Classes;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Handler;
use League\Flysystem\PluginInterface;

/**
 * Add a local file cache on top of our remote filesystem
 * League\Flysystem\Filesystem has cache adapters, but none of
 * them cache the actual file which is needed to avoid going back
 * and forth to S3 a bunch.
 */
class LocalCachingFilesystemDecorator implements FilesystemInterface
{
    /**
     * @var FilesystemInterface
     */
    private $cacheFileSystem;

    /**
     * @var FilesystemInterface
     */
    private $remoteFileSystem;

    /**
     * @var bool
     */
    protected $cacheEnabled;

    public function __construct(FilesystemInterface $cacheFileSystem, FilesystemInterface $remoteFileSystem)
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
     * Re enable the cache
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
        } catch (FileNotFoundException) {
            //ignore this one we don't always have files in the cache
        }
    }

    /**
     * Wrapped deleteFromDir for removing possibly missing files from the local cache
     */
    protected function deleteDirFromCache(string $dirname): void
    {
        try {
            //cleanup any existing test file
            $this->cacheFileSystem->deleteDir($dirname);
        } catch (FileNotFoundException) {
            //ignore this one we don't always have files in the cache
        }
    }

    public function has($path): bool
    {
        return $this->remoteFileSystem->has($path);
    }

    public function read($path): string|false
    {
        if ($this->cacheEnabled && $this->cacheFileSystem->has($path)) {
            return $this->cacheFileSystem->read($path);
        }
        $result = $this->remoteFileSystem->read($path);

        if ($result !== false) {
            $this->cacheFileSystem->put($path, $result);
        }

        return $result;
    }

    public function readStream($path)
    {
        if ($this->cacheEnabled && $this->cacheFileSystem->has($path)) {
            return $this->cacheFileSystem->readStream($path);
        }
        $resource = $this->remoteFileSystem->readStream($path);

        if ($resource !== false) {
            $this->cacheFileSystem->putStream($path, $resource);
        }


        return $resource;
    }

    public function listContents($directory = '', $recursive = false): array
    {
        return $this->remoteFileSystem->listContents($directory, $recursive);
    }

    public function getMetadata($path): array|false
    {
        return $this->remoteFileSystem->getMetadata($path);
    }

    public function getSize($path): int|false
    {
        return $this->remoteFileSystem->getSize($path);
    }

    public function getMimetype($path): string|false
    {
        return $this->remoteFileSystem->getMimetype($path);
    }

    public function getTimestamp($path): int|false
    {
        return $this->remoteFileSystem->getTimestamp($path);
    }

    public function getVisibility($path): string|false
    {
        return $this->remoteFileSystem->getVisibility($path);
    }

    public function write($path, $contents, array $config = []): bool
    {
        $this->remoteFileSystem->write($path, $contents, $config);
        return $this->cacheFileSystem->put($path, $contents, $config);
    }

    public function writeStream($path, $resource, array $config = []): bool
    {
        $this->remoteFileSystem->writeStream($path, $resource, $config);
        return $this->cacheFileSystem->putStream($path, $resource, $config);
    }

    public function update($path, $contents, array $config = []): bool
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->update($path, $contents, $config);
    }

    public function updateStream($path, $resource, array $config = []): bool
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->updateStream($path, $resource, $config);
    }

    public function rename($path, $newpath): bool
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->rename($path, $newpath);
    }

    public function copy($path, $newpath): bool
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->copy($path, $newpath);
    }

    public function delete($path): bool
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->delete($path);
    }

    public function deleteDir($dirname): bool
    {
        $this->deleteDirFromCache($dirname);
        return $this->remoteFileSystem->deleteDir($dirname);
    }

    public function createDir($dirname, array $config = []): bool
    {
        return $this->remoteFileSystem->createDir($dirname, $config);
    }

    public function setVisibility($path, $visibility): bool
    {
        return $this->remoteFileSystem->setVisibility($path, $visibility);
    }

    public function put($path, $contents, array $config = []): bool
    {
        $this->cacheFileSystem->put($path, $contents, $config);
        return $this->remoteFileSystem->put($path, $contents, $config);
    }

    public function putStream($path, $resource, array $config = []): bool
    {
        $this->cacheFileSystem->putStream($path, $resource, $config);
        return $this->remoteFileSystem->putStream($path, $resource, $config);
    }

    public function readAndDelete($path): string|false
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->readAndDelete($path);
    }

    public function get($path, Handler $handler = null): Handler
    {
        return $this->remoteFileSystem->get($path, $handler);
    }

    public function addPlugin(PluginInterface $plugin): static
    {
        return $this->remoteFileSystem->addPlugin($plugin);
    }
}
