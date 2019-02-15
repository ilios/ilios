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
            return $this->getS3Filesystem($s3Url);
        }

        return $this->getLocalFilesystem();
    }

    protected function getS3Filesystem(string $s3Url) : FilesystemInterface
    {
        $configuration = $this->parseS3URL($s3Url);
        //extract bucket from configuration, it's not required here
        $bucket = $configuration['bucket'];
        unset($configuration['bucket']);

        $client = new S3Client($configuration);
        $s3 = new AwsS3Adapter($client, $bucket);
        $localAdapter = $this->getLocalAdapter($this->kernelCacheDir . '/ilios/s3-cache');

        $cache = new Adapter($localAdapter, 'file');
        $s3Adapter = new CachedAdapter($s3, $cache);
        $remoteFileSystem = new LeagueFilesystem($s3Adapter, ['visibility' => 'private']);
        $localCache = new LeagueFilesystem($localAdapter, ['visibility' => 'private']);

        return new LocalCachingFilesystemDecorator($localCache, $remoteFileSystem);
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
