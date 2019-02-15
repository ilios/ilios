<?php

namespace App\Classes;

use League\Flysystem\Filesystem;
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
     * @var Filesystem
     */
    private $cacheFileSystem;

    /**
     * @var Filesystem
     */
    private $remoteFileSystem;

    public function __construct(Filesystem $cacheFileSystem, Filesystem $remoteFileSystem)
    {
        $this->cacheFileSystem = $cacheFileSystem;
        $this->remoteFileSystem = $remoteFileSystem;
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
        if ($this->cacheFileSystem->has($path)) {
            return $this->cacheFileSystem->read($path);
        }
        $string = $this->remoteFileSystem->read($path);
        $this->cacheFileSystem->write($path, $string);

        return $string;
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        if ($this->cacheFileSystem->has($path)) {
            return $this->cacheFileSystem->readStream($path);
        }
        $resource = $this->remoteFileSystem->readStream($path);
        $this->cacheFileSystem->writeStream($path, $resource);

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
        return $this->cacheFileSystem->write($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource, array $config = [])
    {
        $this->remoteFileSystem->writeStream($path, $resource, $config);
        return $this->cacheFileSystem->writeStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function update($path, $contents, array $config = [])
    {
        $this->cacheFileSystem->delete($path);
        return $this->remoteFileSystem->update($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function updateStream($path, $resource, array $config = [])
    {
        $this->cacheFileSystem->delete($path);
        return $this->remoteFileSystem->updateStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newpath)
    {
        $this->cacheFileSystem->delete($path);
        return $this->remoteFileSystem->rename($path, $newpath);
    }

    /**
     * @inheritdoc
     */
    public function copy($path, $newpath)
    {
        $this->cacheFileSystem->delete($path);
        return $this->remoteFileSystem->copy($path, $newpath);
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        $this->cacheFileSystem->delete($path);
        return $this->remoteFileSystem->delete($path);
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirname)
    {
        $this->cacheFileSystem->deleteDir($dirname);
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
        $this->cacheFileSystem->delete($path);
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
