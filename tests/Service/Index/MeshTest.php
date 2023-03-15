<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Service\Config;
use App\Service\Index\Mesh;
use App\Tests\TestCase;
use OpenSearch\Client;
use Exception;
use Ilios\MeSH\Model\Descriptor;
use Mockery as m;

class MeshTest extends TestCase
{
    /**
     * @var Client|m\MockInterface
     */
    private $client;

    /**
     * @var Config|m\MockInterface
     */
    private $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = m::mock(Client::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')
            ->with('search_upload_limit')
            ->andReturn(8000000);
    }
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->config);
    }

    public function testSetup()
    {
        $obj1 = new Mesh($this->config, $this->client);
        $this->assertTrue($obj1 instanceof Mesh);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = new Mesh($this->config, null);
        $this->assertTrue($obj2 instanceof Mesh);
        $this->assertFalse($obj2->isEnabled());
    }

    public function testIndexMeshDescriptorsWorksWithoutSearch()
    {
        $desc1 = m::mock(Descriptor::class)
            ->shouldReceive('getConcepts')->once()->andReturn([])
            ->shouldReceive('getUi')->once()->andReturn('id')
            ->shouldReceive('getName')->once()->andReturn('name')
            ->shouldReceive('getAnnotation')->once()->andReturn('annt')
            ->shouldReceive('getPreviousIndexing')->once()->andReturn(['pi'])
            ->getMock();
        $obj = new Mesh($this->config, null);
        $desc2 = m::mock(Descriptor::class)
            ->shouldReceive('getConcepts')->once()->andReturn([])
            ->shouldReceive('getUi')->once()->andReturn('id')
            ->shouldReceive('getName')->once()->andReturn('name')
            ->shouldReceive('getAnnotation')->once()->andReturn('annt')
            ->shouldReceive('getPreviousIndexing')->once()->andReturn(['pi'])
            ->getMock();
        $obj = new Mesh($this->config, null);
        $arr = [
            $desc1,
            $desc2,
        ];
        $this->assertTrue($obj->index($arr));
    }

    public function testIdsQueryThrowsExceptionWhenNotConfigured()
    {
        $obj = new Mesh($this->config, null);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->idsQuery('');
    }
}
