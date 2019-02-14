<?php

namespace App\Service;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Adapter;
use League\Flysystem\Filesystem as LeagueFilesystem;

class FilesystemFactory
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function getFilesystem() : LeagueFilesystem
    {
        $s3Url = $this->config->get('storage_s3_url');
        $path = $this->config->get('file_system_storage_path');

        $local = new Local($path);

        if ($s3Url) {
            $configuration = $this->parseS3URL($s3Url);
            //extract bucket from configuration, it's not required here
            $bucket = $configuration['bucket'];
            unset($configuration['bucket']);

            $client = new S3Client($configuration);
            $s3 = new AwsS3Adapter($client, $bucket);

            $cache = new Adapter($local, 'ilios-s3-cache');
            $adapter = new CachedAdapter($s3, $cache);
            return new LeagueFilesystem($adapter, ['visibility' => 'private']);
        }

        return new LeagueFilesystem($local, ['visibility' => 'private']);
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
