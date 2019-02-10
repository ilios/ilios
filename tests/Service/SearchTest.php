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
        $obj1 = $this->createWithHost();
        $this->assertTrue($obj1 instanceof Search);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = $this->createWithoutHost();
        $this->assertTrue($obj2 instanceof Search);
        $this->assertFalse($obj2->isEnabled());
    }

    public function testUserIdsQueryThrowsExceptionWhenNotConfigured()
    {
        $obj = $this->createWithoutHost();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->userIdsQuery('');
    }

    protected function createWithHost()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('elasticsearch_hosts')->once()->andReturn('host');
        return new Search($config);
    }

    protected function createWithoutHost()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('elasticsearch_hosts')->once()->andReturn(false);
        return new Search($config);
    }
}
