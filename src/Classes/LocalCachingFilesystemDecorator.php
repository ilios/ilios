<?php

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
    public function disableCache() : void
    {
        $this->cacheEnabled = false;
    }

    /**
     * Re enable the cache
     */
    public function enableCache() : void
    {
        $this->cacheEnabled = true;
    }

    /**
     * Wrapped delete for removing possibly missing files from the local cache
     * @param $path
     */
    protected function deleteFromCache(string $path) : void
    {
        try {
            //cleanup any existing test file
            $this->cacheFileSystem->delete($path);
        } catch (FileNotFoundException $e) {
            //ignore this one we don't always have files in the cache
        }
    }

    /**
     * Wrapped deleteFromDir for removing possibly missing files from the local cache
     * @param $dirname
     */
    protected function deleteDirFromCache(string $dirname) : void
    {
        try {
            //cleanup any existing test file
            $this->cacheFileSystem->deleteDir($dirname);
        } catch (FileNotFoundException $e) {
            //ignore this one we don't always have files in the cache
        }
    }

    /**
     * @inheritdoc
     */
    public function has($path)
    {
        return $this->remoteFileSystem->has($path);
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        if ($this->cacheEnabled && $this->cacheFileSystem->has($path)) {
            return $this->cacheFileSystem->read($path);
        }
        $string = $this->remoteFileSystem->read($path);
        $this->cacheFileSystem->put($path, $string);

        return $string;
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        if ($this->cacheEnabled && $this->cacheFileSystem->has($path)) {
            return $this->cacheFileSystem->readStream($path);
        }
        $resource = $this->remoteFileSystem->readStream($path);
        $this->cacheFileSystem->putStream($path, $resource);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->remoteFileSystem->listContents($directory, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        return $this->remoteFileSystem->getMetadata($path);
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        return $this->remoteFileSystem->getSize($path);
    }

    /**
     * @inheritdoc
     */
    public function getMimetype($path)
    {
        return $this->remoteFileSystem->getMimetype($path);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        return $this->remoteFileSystem->getTimestamp($path);
    }

    /**
     * @inheritdoc
     */
    public function getVisibility($path)
    {
        return $this->remoteFileSystem->getVisibility($path);
    }

    /**
     * @inheritdoc
     */
    public function write($path, $contents, array $config = [])
    {
        $this->remoteFileSystem->write($path, $contents, $config);
        return $this->cacheFileSystem->put($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource, array $config = [])
    {
        $this->remoteFileSystem->writeStream($path, $resource, $config);
        return $this->cacheFileSystem->putStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function update($path, $contents, array $config = [])
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->update($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function updateStream($path, $resource, array $config = [])
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->updateStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newpath)
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->rename($path, $newpath);
    }

    /**
     * @inheritdoc
     */
    public function copy($path, $newpath)
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->copy($path, $newpath);
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->delete($path);
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirname)
    {
        $this->deleteDirFromCache($dirname);
        return $this->remoteFileSystem->deleteDir($dirname);
    }

    /**
     * @inheritdoc
     */
    public function createDir($dirname, array $config = [])
    {
        return $this->remoteFileSystem->createDir($dirname, $config);
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        return $this->remoteFileSystem->setVisibility($path, $visibility);
    }

    /**
     * @inheritdoc
     */
    public function put($path, $contents, array $config = [])
    {
        $this->cacheFileSystem->put($path, $contents, $config);
        return $this->remoteFileSystem->put($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function putStream($path, $resource, array $config = [])
    {
        $this->cacheFileSystem->putStream($path, $resource, $config);
        return $this->remoteFileSystem->putStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function readAndDelete($path)
    {
        $this->deleteFromCache($path);
        return $this->remoteFileSystem->readAndDelete($path);
    }

    /**
     * @inheritdoc
     */
    public function get($path, Handler $handler = null)
    {
        return $this->remoteFileSystem->get($path, $handler);
    }

    /**
     * @inheritdoc
     */
    public function addPlugin(PluginInterface $plugin)
    {
        return $this->remoteFileSystem->addPlugin($plugin);
    }
}
