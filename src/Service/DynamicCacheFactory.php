<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Symfony\Component\Cache\Marshaller\DeflateMarshaller;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;
use Symfony\Component\Cache\Marshaller\SodiumMarshaller;

class DynamicCacheFactory
{
    private const int DEFAULT_LIFETIME_SECONDS = 86400;

    public static function getCache(
        string $namespace,
        ?string $redisUrl,
        string $environment,
        string $kernelCacheDirectory,
        ?string $cacheDecryptionKey,
    ): TagAwareAdapterInterface {
        $marshaller = self::getMarshaller($cacheDecryptionKey);

        if ($environment === 'test') {
            return new TagAwareAdapter(new NullAdapter());
        }

        if ($redisUrl) {
            $client = RedisAdapter::createConnection($redisUrl);
            return new RedisTagAwareAdapter(
                $client,
                $namespace,
                self::DEFAULT_LIFETIME_SECONDS,
                $marshaller,
            );
        }
        return new FilesystemTagAwareAdapter(
            $namespace,
            self::DEFAULT_LIFETIME_SECONDS,
            $kernelCacheDirectory,
            $marshaller,
        );
    }

    /**
     * By default, encode and compress any data, but if an encryption key is
     * provided we also encrypt data at rest.
     */
    protected static function getMarshaller(?string $key): MarshallerInterface
    {
        $marshaller = new DeflateMarshaller(new DefaultMarshaller());
        if (!$key) {
            return $marshaller;
        }
        return new SodiumMarshaller([$key], $marshaller);
    }
}
