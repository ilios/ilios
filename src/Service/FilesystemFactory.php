<?php

namespace App\Service;

use App\Classes\LocalCachingFilesystemDecorator;
use Aws\S3\S3Client;
use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Adapter;
use League\Flysystem\Cached\Storage\Memory as MemoryStore;
use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\FilesystemInterface;

class FilesystemFactory
{
    const LOCAL_S3_CACHE_DIR = '/ilios/s3-cache';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    private $kernelCacheDir;

    public function __construct(
        Config $config,
        string $kernelCacheDir
    ) {
        $this->config = $config;
        $this->kernelCacheDir = $kernelCacheDir;
    }

    public function getFilesystem() : FilesystemInterface
    {
        $s3Url = $this->config->get('storage_s3_url');

        if ($s3Url) {
            return $this->getS3FilesystemWithCache($s3Url);
        }

        return $this->getLocalFilesystem();
    }

    public function getNonCachingFilesystem() : FilesystemInterface
    {
        $s3Url = $this->config->get('storage_s3_url');

        if ($s3Url) {
            $adapter = $this->getS3Adapter($s3Url);
            return new LeagueFilesystem($adapter);
        }

        $path = $this->config->get('file_system_storage_path');
        $localAdapter = new Local($path);

        return new LeagueFilesystem($localAdapter, ['visibility' => 'private']);
    }

    /**
     * Get the path to the S3 cache
     * @return string path
     */
    public function getLocalS3CacheDirectory() : string
    {
        return $this->kernelCacheDir . self::LOCAL_S3_CACHE_DIR;
    }

    /**
     * Get a filesystem for the local S3 cache
     * @return FilesystemInterface
     */
    public function getS3LocalFilesystemCache() : FilesystemInterface
    {
        $localAdapter = $this->getLocalAdapter($this->getLocalS3CacheDirectory());
        return new LeagueFilesystem($localAdapter, ['visibility' => 'private']);
    }

    protected function getS3FilesystemWithCache(string $s3Url) : FilesystemInterface
    {
        $s3 = $this->getS3Adapter($s3Url);
        $localAdapter = $this->getLocalAdapter($this->getLocalS3CacheDirectory());

        $cache = new Adapter($localAdapter, 'file');
        $s3Adapter = new CachedAdapter($s3, $cache);
        $remoteFileSystem = new LeagueFilesystem($s3Adapter, ['visibility' => 'private']);
        $localCache = $this->getS3LocalFilesystemCache();

        return new LocalCachingFilesystemDecorator($localCache, $remoteFileSystem);
    }

    protected function getS3Adapter(string $s3Url) : AwsS3Adapter
    {
        $configuration = $this->parseS3URL($s3Url);
        //extract bucket from configuration, it's not required here
        $bucket = $configuration['bucket'];
        unset($configuration['bucket']);

        $client = new S3Client($configuration);

        return new AwsS3Adapter($client, $bucket);
    }

    protected function getLocalAdapter($path) : AdapterInterface
    {
        $localAdapter = new Local($path);
        $cacheStore = new MemoryStore();
        return new CachedAdapter($localAdapter, $cacheStore);
    }

    protected function getLocalFilesystem() : FilesystemInterface
    {
        $path = $this->config->get('file_system_storage_path');
        $adapter = $this->getLocalAdapter($path);

        return new LeagueFilesystem($adapter, ['visibility' => 'private']);
    }

    protected function parseS3URL(string $url) : array
    {
        $result = preg_match(
            '#^(s3)://([a-zA-Z0-9]+):([A-Za-z0-9/+=]{40})@([a-z0-9-]+.)\.([a-z0-9-]+)#',
            $url,
            $matches
        );
        if (!$result) {
            throw new \Exception("Bad S3 URL, should be formatted as `s3://KEY:SECRET@bucket.region`");
        }

        return [
            'credentials' => [
                'key'    => $matches[2],
                'secret' => $matches[3],
            ],
            'region' => $matches[5],
            'version' => 'latest',
            'bucket' => $matches[4]
        ];
    }
}
