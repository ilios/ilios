<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class DynamicCacheFactory
{
    private const DEFAULT_LIFETIME_SECONDS = 86400;

    public static function getCache($namespace, $redisUrl, $kernelCacheDirectory): TagAwareAdapterInterface
    {
        if ($redisUrl) {
            $client = RedisAdapter::createConnection($redisUrl);
            return new RedisTagAwareAdapter(
                $client,
                $namespace,
                self::DEFAULT_LIFETIME_SECONDS
            );
        }
        return new FilesystemTagAwareAdapter(
            $namespace,
            self::DEFAULT_LIFETIME_SECONDS,
            $kernelCacheDirectory,
        );
    }
}
