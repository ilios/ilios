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
        if ($environment === 'prod') {
            $cache = new ApcuCache();
        } else {
            $cache = new ArrayCache();
        }

        return $cache;
    }
}
