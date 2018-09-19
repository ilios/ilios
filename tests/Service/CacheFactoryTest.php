<?php
namespace App\Tests\Service;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use App\Service\CacheFactory;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class CacheFactoryTest extends TestCase
{
    /**
     * @covers \App\Service\CacheFactory::createCache
     */
    public function testCreateDev()
    {
        $cache = CacheFactory::createCache('dev');
        $this->assertInstanceOf(ArrayCache::class, $cache);
    }

    /**
     * @covers \App\Service\CacheFactory::createCache
     */
    public function testCreateProd()
    {
        $cache = CacheFactory::createCache('prod');
        $this->assertInstanceOf(ApcuCache::class, $cache);
    }
    /**
     * @covers \App\Service\CacheFactory::createCache
     */
    public function testCreateTest()
    {
        $cache = CacheFactory::createCache('test');
        $this->assertInstanceOf(ApcuCache::class, $cache);
    }
}
