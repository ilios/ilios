<?php
namespace App\Tests\Service;

use App\Service\Config;
use App\Service\Search;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

class SearchTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testSetup()
    {
        $obj1 = $this->createWithSearch();
        $this->assertTrue($obj1 instanceof Search);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = $this->createWithoutSearch();
        $this->assertTrue($obj2 instanceof Search);
        $this->assertFalse($obj2->isEnabled());
    }

    public function testIndexReturnsEmptyWhenNotConfigured()
    {
        $obj = $this->createWithoutSearch();
        $result = $obj->index([]);
        $this->assertTrue(is_array($result));
        $this->assertEmpty($result);
    }

    public function testDeleteReturnsEmptyWhenNotConfigured()
    {
        $obj = $this->createWithoutSearch();
        $result = $obj->delete([]);
        $this->assertTrue(is_array($result));
        $this->assertEmpty($result);
    }

    public function testBulkReturnsEmptyWhenNotConfigured()
    {
        $obj = $this->createWithoutSearch();
        $result = $obj->bulk([]);
        $this->assertTrue(is_array($result));
        $this->assertEmpty($result);
    }

    public function testBulkIndexReturnsEmptyWhenNotConfigured()
    {
        $obj = $this->createWithoutSearch();
        $result = $obj->bulkIndex('nothing', 'nothing', []);
        $this->assertTrue(is_array($result));
        $this->assertEmpty($result);
    }

    public function testSearchThrowsExceptionWhenNotConfigured()
    {
        $obj = $this->createWithoutSearch();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->search([]);
    }

    public function testClearThrowsExceptionWhenNotConfigured()
    {
        $obj = $this->createWithoutSearch();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->clear();
    }

    public function testUserIdsQueryThrowsExceptionWhenNotConfigured()
    {
        $obj = $this->createWithoutSearch();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->userIdsQuery('');
    }

    protected function createWithSearch()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('elasticsearch_hosts')->once()->andReturn('host');
        return new Search($config);
    }

    protected function createWithoutSearch()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('elasticsearch_hosts')->once()->andReturn(false);
        return new Search($config);
    }
}
