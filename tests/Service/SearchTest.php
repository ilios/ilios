<?php
namespace App\Tests\Service;

use App\Service\Config;
use App\Service\Search;
use Elasticsearch\Client;
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

    public function testMeshDescriptorIdsQueryThrowsExceptionWhenNotConfigured()
    {
        $obj = $this->createWithoutHost();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->meshDescriptorIdsQuery('');
    }

    protected function createWithHost()
    {
        $client = m::mock(Client::class);
        return new Search($client);
    }

    protected function createWithoutHost()
    {
        return new Search(null);
    }
}
