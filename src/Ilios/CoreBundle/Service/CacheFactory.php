<?php

namespace Ilios\CoreBundle\Service;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;

class CacheFactory
{

    /**
     * @param $environment
     * @return Cache
     */
    public static function createCache($environment)
    {
        if ($environment === 'dev') {
            $cache = new ArrayCache();
        } else {
            $cache = new ApcuCache();
        }

        return $cache;
    }
}
