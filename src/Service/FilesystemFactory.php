<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\LocalCachingFilesystemDecorator;
use Aws\S3\S3Client;
use Exception;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;

class FilesystemFactory
{
    private const string LOCAL_S3_CACHE_DIR = '/ilios/s3-cache';

    public function __construct(protected Config $config, private string $kernelCacheDir)
    {
    }

    public function getFilesystem(): FilesystemOperator
    {
        $s3Url = $this->config->get('storage_s3_url');

        if ($s3Url) {
            return $this->getS3FilesystemWithCache($s3Url);
        }

        return $this->getLocalFilesystem();
    }

    public function getNonCachingFilesystem(): FilesystemOperator
    {
        $s3Url = $this->config->get('storage_s3_url');

        if ($s3Url) {
            $adapter = $this->getS3Adapter($s3Url);
            return new Filesystem($adapter);
        }

        $path = $this->config->get('file_system_storage_path');
        $localAdapter = new LocalFilesystemAdapter($path);

        return new Filesystem($localAdapter, ['visibility' => 'private']);
    }

    /**
     * Get the path to the S3 cache
     */
    public function getLocalS3CacheDirectory(): string
    {
        return $this->kernelCacheDir . self::LOCAL_S3_CACHE_DIR;
    }

    /**
     * Get a filesystem for the local S3 cache
     */
    public function getS3LocalFilesystemCache(): FilesystemOperator
    {
        $localAdapter = $this->getLocalAdapter($this->getLocalS3CacheDirectory());
        return new Filesystem($localAdapter, ['visibility' => 'private']);
    }

    protected function getS3FilesystemWithCache(string $s3Url): FilesystemOperator
    {
        $s3 = $this->getS3Adapter($s3Url);
        $remoteFileSystem = new Filesystem($s3, ['visibility' => 'private']);
        $localCache = $this->getS3LocalFilesystemCache();

        return new LocalCachingFilesystemDecorator($localCache, $remoteFileSystem);
    }

    protected function getS3Adapter(string $s3Url): AwsS3V3Adapter
    {
        $configuration = $this->parseS3URL($s3Url);
        //extract bucket from configuration, it's not required here
        $bucket = $configuration['bucket'];
        unset($configuration['bucket']);

        $client = new S3Client($configuration);

        return new AwsS3V3Adapter($client, $bucket);
    }

    protected function getLocalAdapter(string $path): LocalFilesystemAdapter
    {
        return new LocalFilesystemAdapter($path);
    }

    protected function getLocalFilesystem(): FilesystemOperator
    {
        $path = $this->config->get('file_system_storage_path');
        $adapter = $this->getLocalAdapter($path);

        return new Filesystem($adapter, ['visibility' => 'private']);
    }

    protected function parseS3URL(string $url): array
    {
        $result = preg_match(
            '#^(s3)://([a-zA-Z0-9]+):([A-Za-z0-9/+=]{40})@([a-z0-9-]+.)\.([a-z0-9-]+)#',
            $url,
            $matches
        );
        if (!$result) {
            throw new Exception("Bad S3 URL, should be formatted as `s3://KEY:SECRET@bucket.region`");
        }

        return [
            'credentials' => [
                'key'    => $matches[2],
                'secret' => $matches[3],
            ],
            'region' => $matches[5],
            'version' => 'latest',
            'bucket' => $matches[4],
        ];
    }
}
